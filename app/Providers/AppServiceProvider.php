<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        \Illuminate\Pagination\Paginator::useTailwind();

        // Register event listeners
        \Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\RecordLoginActivity::class,
        );

        // Register observers
    }
}
