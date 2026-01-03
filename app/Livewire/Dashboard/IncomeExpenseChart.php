<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use App\Models\Operation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncomeExpenseChart extends Component
{
    public $period = '30J'; // Default: 30 days
    public $chartData = [];

    public function mount()
    {
        $this->updateChartData();
    }

    public function setPeriod($period)
    {
        $this->period = $period;
        $this->updateChartData();
    }

    private function updateChartData()
    {
        $this->chartData = $this->calculateData();
    }

    private function calculateData()
    {
        $tenantId = auth()->user()->tenant_id;
        $query = Operation::where('tenant_id', $tenantId)
            ->validated();

        $startDate = now();
        $labels = [];

        switch ($this->period) {
            case '7J':
                $startDate = now()->subDays(6);
                break;
            case '30J':
                $startDate = now()->subDays(29);
                break;
            case '90J':
                $startDate = now()->subDays(89);
                break;
            case 'MONTHLY':
                $startDate = now()->startOfMonth();
                break;
            case 'YEARLY':
                $startDate = now()->startOfYear();
                break;
        }

        $operations = $query->where('operation_date', '>=', $startDate->toDateString())
            ->orderBy('operation_date')
            ->get();

        // Group by date or month depending on period
        $grouped = [];
        
        if ($this->period === 'YEARLY') {
            // Group by month
            $current = $startDate->copy();
            while ($current <= now()) {
                $monthKey = $current->format('Y-m');
                $labels[] = $current->translatedFormat('M');
                $grouped[$monthKey] = ['income' => 0, 'expense' => 0];
                $current->addMonth();
            }

            foreach ($operations as $op) {
                $key = $op->operation_date->format('Y-m');
                if (isset($grouped[$key])) {
                    if ($op->type === Operation::TYPE_INCOME) {
                        $grouped[$key]['income'] += $op->converted_amount;
                    } elseif ($op->type === Operation::TYPE_EXPENSE) {
                        $grouped[$key]['expense'] += $op->converted_amount;
                    }
                }
            }
        } else {
            // Group by day
            $current = $startDate->copy();
            while ($current <= now()) {
                $dayKey = $current->format('Y-m-d');
                $labels[] = $current->format('d/m');
                $grouped[$dayKey] = ['income' => 0, 'expense' => 0];
                $current->addDay();
            }

            foreach ($operations as $op) {
                $key = $op->operation_date->format('Y-m-d');
                if (isset($grouped[$key])) {
                    if ($op->type === Operation::TYPE_INCOME) {
                        $grouped[$key]['income'] += $op->converted_amount;
                    } elseif ($op->type === Operation::TYPE_EXPENSE) {
                        $grouped[$key]['expense'] += $op->converted_amount;
                    }
                }
            }
        }

        return [
            'labels' => $labels,
            'income' => array_column($grouped, 'income'),
            'expense' => array_column($grouped, 'expense'),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.income-expense-chart');
    }
}
