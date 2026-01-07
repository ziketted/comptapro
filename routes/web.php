<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Google OAuth routes
Route::get('/auth/google', [App\Http\Controllers\Auth\GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\Auth\GoogleAuthController::class, 'handleGoogleCallback']);

// Organization setup route (for users without organization)
Route::middleware(['auth'])->group(function () {
    Route::get('/organization/setup', \App\Livewire\Organization\Setup::class)->name('organization.setup');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Transaction routes
    Route::resource('transactions', TransactionController::class);
    Route::post('transactions/{transaction}/validate', [TransactionController::class, 'validate'])->name('transactions.validate');
    Route::post('transactions/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');

    // Cashbook routes (Manager only)
    Route::middleware('manager')->get('/cashbook', \App\Livewire\Cashbook\Index::class)->name('cashbook.index');

    // Exchange routes
    Route::prefix('exchange')->name('exchange.')->group(function () {
        Route::get('/', \App\Livewire\Exchange\Create::class)->name('index');
        Route::get('/create', \App\Livewire\Exchange\Create::class)->name('create');
    });

    // Operations routes
    Route::prefix('operations')->name('operations.')->group(function () {
        Route::get('/', \App\Livewire\Operations\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Operations\Create::class)->name('create');
        Route::get('/{operation}/edit', \App\Livewire\Operations\Edit::class)->name('edit')->middleware('manager');
        
        Route::get('/validate', \App\Livewire\Operations\Validate::class)->name('validate');
    });

    // Accounts routes
    Route::prefix('accounts')->name('accounts.')->group(function () {
        Route::get('/{type?}', \App\Livewire\Accounts\Index::class)->name('index');
    });

    // Beneficiaries routes
    Route::prefix('beneficiaries')->name('beneficiaries.')->group(function () {
        Route::get('/', \App\Livewire\Beneficiaries\Index::class)->name('index');
    });

    // Settings routes (Manager only)
    Route::middleware('manager')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/', fn() => redirect()->route('settings.features'))->name('index');
        Route::get('/features', \App\Livewire\Settings\TenantFeatures::class)->name('features');
        Route::get('/currency', \App\Livewire\Settings\Currency::class)->name('currency');
        Route::get('/cashboxes', \App\Livewire\Settings\Cashboxes::class)->name('cashboxes');
        Route::get('/users', \App\Livewire\Settings\Users::class)->name('users');
    });

    // Reports routes (Manager only - check in component)
    // Reports (Manager only)
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [App\Http\Controllers\ReportsController::class, 'index'])->name('index');
        
        Route::get('/cash-journal', [App\Http\Controllers\ReportsController::class, 'cashJournal'])->name('cash-journal');
        Route::get('/cash-journal/pdf', [App\Http\Controllers\ReportsController::class, 'exportCashJournalPdf'])->name('cash-journal.pdf');
        Route::get('/cash-journal/excel', [App\Http\Controllers\ReportsController::class, 'exportCashJournalExcel'])->name('cash-journal.excel');
        
        Route::get('/account-report', [App\Http\Controllers\ReportsController::class, 'accountReport'])->name('account-report');
        Route::get('/account-report/pdf', [App\Http\Controllers\ReportsController::class, 'exportAccountReportPdf'])->name('account-report.pdf');
        Route::get('/account-report/excel', [App\Http\Controllers\ReportsController::class, 'exportAccountReportExcel'])->name('account-report.excel');
        
        Route::get('/balance', [App\Http\Controllers\ReportsController::class, 'balanceAtDate'])->name('balance');
        Route::get('/balance/pdf', [App\Http\Controllers\ReportsController::class, 'exportBalancePdf'])->name('balance.pdf');
        Route::get('/balance/excel', [App\Http\Controllers\ReportsController::class, 'exportBalanceExcel'])->name('balance.excel');
        
        Route::get('/profit-loss', [App\Http\Controllers\ReportsController::class, 'profitLoss'])->name('profit-loss');
        Route::get('/profit-loss/pdf', [App\Http\Controllers\ReportsController::class, 'exportProfitLossPdf'])->name('profit-loss.pdf');
        Route::get('/profit-loss/excel', [App\Http\Controllers\ReportsController::class, 'exportProfitLossExcel'])->name('profit-loss.excel');
    });

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
