<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\ExportService;
use App\Models\Cashbox;
use App\Models\Account;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    protected $reportService;
    protected $exportService;

    public function __construct(ReportService $reportService, ExportService $exportService)
    {
        $this->reportService = $reportService;
        $this->exportService = $exportService;
    }

    public function index()
    {
        if (!auth()->user()->isManager()) {
            abort(403);
        }
        return view('reports.index');
    }

    // --- Cash Journal ---

    public function cashJournal(Request $request)
    {
        if (!auth()->user()->isManager()) abort(403);

        $filters = $request->only(['start_date', 'end_date', 'type', 'account_id', 'currency_id', 'beneficiary_search', 'group_by']);
        $result = $this->reportService->getCashJournal($filters);
        
        $operations = $result['operations'];
        $totals = $result['totals'];
        
        // Data for filters
        $accounts = Account::where('tenant_id', auth()->user()->tenant_id)->get();
        $currencies = \App\Models\Currency::where('tenant_id', auth()->user()->tenant_id)->get();

        return view('reports.cash-journal', compact('operations', 'totals', 'accounts', 'currencies', 'filters'));
    }

    public function exportCashJournalPdf(Request $request)
    {
        if (!auth()->user()->isManager()) abort(403);
        
        $filters = $request->only(['start_date', 'end_date', 'type', 'account_id', 'currency_id', 'beneficiary_search', 'group_by']);
        $result = $this->reportService->getCashJournal($filters, false); // No pagination
        
        $data = [
            'operations' => $result['operations'],
            'totals' => $result['totals'],
            'filters' => $filters,
            'title' => 'Journal de Caisse',
            'date' => now()->format('d/m/Y H:i')
        ];

        return $this->exportService->generatePdf('reports.exports.cash-journal-pdf', $data, 'journal_caisse.pdf');
    }

    public function exportCashJournalExcel(Request $request)
    {
        if (!auth()->user()->isManager()) abort(403);

        $filters = $request->only(['start_date', 'end_date', 'type', 'account_id', 'currency_id', 'beneficiary_search', 'group_by']);
        $result = $this->reportService->getCashJournal($filters, false); // No pagination

        $data = $result['operations']->map(function($op) {
            return [
                $op->operation_date->format('d/m/Y'),
                $op->type === 'INCOME' ? 'Recette' : ($op->type === 'EXPENSE' ? 'Dépense' : 'Transfert'),
                $op->account ? $op->account->name : '-',
                $op->beneficiary ? $op->beneficiary->name : '-',
                $op->original_amount . ' ' . $op->currency->code,
                $op->description
            ];
        });

        $headers = ['Date', 'Type', 'Compte', 'Bénéficiaire', 'Montant', 'Description'];

        return $this->exportService->generateExcel($data, $headers, 'journal_caisse.xlsx');
    }

    // --- Account Report ---

    public function accountReport(Request $request)
    {
        if (!auth()->user()->isManager()) abort(403);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $data = $this->reportService->getAccountReport($startDate, $endDate);

        return view('reports.account-report', array_merge($data, ['start_date' => $startDate, 'end_date' => $endDate]));
    }

    public function exportAccountReportPdf(Request $request)
    {
        if (!auth()->user()->isManager()) abort(403);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $data = $this->reportService->getAccountReport($startDate, $endDate);

        $viewData = array_merge($data, [
            'title' => 'Rapport par Compte',
            'date' => now()->format('d/m/Y H:i'),
            'filters' => ['start_date' => $startDate, 'end_date' => $endDate]
        ]);

        return $this->exportService->generatePdf('reports.exports.account-report-pdf', $viewData, 'rapport_comptes.pdf');
    }

    public function exportAccountReportExcel(Request $request)
    {
         if (!auth()->user()->isManager()) abort(403);
         // Simplified Excel for accounts: List all accounts and totals
         $startDate = $request->input('start_date');
         $endDate = $request->input('end_date');
         $reportData = $this->reportService->getAccountReport($startDate, $endDate);
         
         $excelData = collect();
         
         // Income Headers
         $excelData->push(['RECETTES', '', '']);
         foreach($reportData['income'] as $row) {
             $excelData->push([
                 $row['account']->account_number,
                 $row['account']->name,
                 $row['total'] . ' ' . $reportData['baseCurrency']
             ]);
         }
         
         // Expense Headers
         $excelData->push(['', '', '']);
         $excelData->push(['DEPENSES', '', '']);
         foreach($reportData['expense'] as $row) {
             $excelData->push([
                 $row['account']->account_number,
                 $row['account']->name,
                 $row['total'] . ' ' . $reportData['baseCurrency']
             ]);
         }

         $headers = ['Numéro', 'Compte', 'Total (' . $reportData['baseCurrency'] . ')'];
         
         return $this->exportService->generateExcel($excelData, $headers, 'rapport_comptes.xlsx');
    }


    // --- Balance At Date ---

    public function balanceAtDate(Request $request)
    {
        if (!auth()->user()->isManager()) abort(403);
        
        $date = $request->input('date', now()->format('Y-m-d'));
        $balances = $this->reportService->getBalanceAtDate($date);

        return view('reports.balance-at-date', compact('balances', 'date'));
    }
    
    public function exportBalancePdf(Request $request)
    {
        if (!auth()->user()->isManager()) abort(403);
        
        $date = $request->input('date', now()->format('Y-m-d'));
        $balances = $this->reportService->getBalanceAtDate($date);
        
        $data = [
            'balances' => $balances,
            'date' => $date,
            'title' => 'Solde à date'
        ];
        
        return $this->exportService->generatePdf('reports.exports.balance-pdf', $data, 'solde_date.pdf');
    }
    
    public function exportBalanceExcel(Request $request)
    {
        if (!auth()->user()->isManager()) abort(403);
        
        $date = $request->input('date', now()->format('Y-m-d'));
        $balances = $this->reportService->getBalanceAtDate($date);
        
        $data = $balances->map(function($item) {
           return [
               $item['cashbox'],
               $item['balance'] . ' ' . $item['currency'],
               number_format($item['balance_base'], 2),
               number_format($item['rate_used'], 4)
           ]; 
        });
        
        $headers = ['Caisse', 'Solde (Original)', 'Contre-valeur Ref', 'Taux utilisé'];
        
        return $this->exportService->generateExcel($data, $headers, 'solde_date.xlsx');
    }

    // --- Profit & Loss ---

    public function profitLoss(Request $request)
    {
        if (!auth()->user()->isManager()) abort(403);
        
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $data = $this->reportService->getProfitLoss($startDate, $endDate);
        
        return view('reports.profit-loss', array_merge($data, ['start_date' => $startDate, 'end_date' => $endDate]));
    }
    
    public function exportProfitLossPdf(Request $request)
    {
        if (!auth()->user()->isManager()) abort(403);
        
         $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $data = $this->reportService->getProfitLoss($startDate, $endDate);
        
        $viewData = array_merge($data, [
            'title' => 'Résultat Simplifié',
             'filters' => ['start_date' => $startDate, 'end_date' => $endDate]
        ]);
        
        return $this->exportService->generatePdf('reports.exports.profit-loss-pdf', $viewData, 'resultat_simplifie.pdf');
    }
    
    public function exportProfitLossExcel(Request $request)
    {
         if (!auth()->user()->isManager()) abort(403);
         
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        
        $data = $this->reportService->getProfitLoss($startDate, $endDate);
        
        $exportData = collect([
            ['Total Recettes', $data['total_income'] . ' ' . $data['base_currency']],
            ['Total Dépenses', $data['total_expense'] . ' ' . $data['base_currency']],
            ['Résultat Net', $data['net_result'] . ' ' . $data['base_currency']],
        ]);
        
        $headers = ['Rubrique', 'Montant'];
        
        return $this->exportService->generateExcel($exportData, $headers, 'resultat_simplifie.xlsx');
    }
}
