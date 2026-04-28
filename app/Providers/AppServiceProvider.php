<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        View::share('totalExpenseAmount', 0);

        // Resolve currency symbol per-request from the logged-in user's restaurant.
        // Controllers can still override by passing currency_symbol explicitly.
        View::composer('*', function ($view) {
            if (array_key_exists('currency_symbol', $view->getData())) {
                return;
            }
            $symbol = '₹';
            try {
                $user = auth()->user();
                if ($user && $user->restaurant_id) {
                    $symbol = optional($user->restaurant)->currencySymbol() ?? '₹';
                }
            } catch (\Throwable $e) {
                // Auth guard unavailable during certain artisan/bootstrap phases
            }
            $view->with('currency_symbol', $symbol);
        });
    }
}
