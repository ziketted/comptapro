@props(['tenant'])

@php
    $daysRemaining = 0;
    $isExpiring = false;
    $message = '';
    
    if ($tenant->status === 'TRIAL') {
        $daysRemaining = $tenant->getTrialDaysRemaining();
        if ($daysRemaining <= 1) {
            $isExpiring = true;
            $message = "Votre période d'essai expire dans " . ($daysRemaining == 0 ? "moins de 24h" : "1 jour") . ". Abonnez-vous pour éviter toute interruption.";
        }
    } elseif ($tenant->subscription_ends_at) {
        $daysRemaining = now()->diffInDays($tenant->subscription_ends_at, false);
        if ($daysRemaining <= 7 && $daysRemaining >= 0) {
            $isExpiring = true;
            $message = "Votre abonnement expire dans " . $daysRemaining . " jours (" . $tenant->subscription_ends_at->format('d/m/Y') . ").";
        }
    }
@endphp

@if($isExpiring)
<div class="bg-orange-50 border-b border-orange-200 dark:bg-orange-900/20 dark:border-orange-800">
    <div class="max-w-7xl mx-auto py-2 px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between flex-wrap">
            <div class="w-0 flex-1 flex items-center">
                <span class="flex p-2 rounded-lg bg-orange-100 dark:bg-orange-800">
                    <span class="iconify text-orange-600 dark:text-orange-200" data-icon="lucide:alert-triangle" style="width: 20px; height: 20px;"></span>
                </span>
                <p class="ml-3 font-medium text-orange-700 dark:text-orange-200 truncate">
                    <span class="md:hidden">Expiration proche !</span>
                    <span class="hidden md:inline">{{ $message }}</span>
                </p>
            </div>
            <div class="order-3 mt-2 flex-shrink-0 w-full sm:order-2 sm:mt-0 sm:w-auto">
                <a href="{{ route('subscription.index') }}" class="flex items-center justify-center px-4 py-1.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-orange-600 bg-white hover:bg-orange-50 dark:bg-slate-800 dark:text-orange-400 dark:hover:bg-slate-700">
                    Prolonger
                </a>
            </div>
        </div>
    </div>
</div>
@endif
