<?php

namespace App\Providers;

use App\Services\RazorpayRouteService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Razorpay Route service needs master keys from config/.env.
        // Controllers type-hint RazorpayRouteService, so bind it here.
        $this->app->singleton(RazorpayRouteService::class, function () {
            $keyId = (string) config('services.razorpay.master_key_id', '');
            $keySecret = (string) config('services.razorpay.master_key_secret', '');

            if ($keyId === '' || $keySecret === '') {
                // Let controllers handle "master not configured" gracefully.
                // We still return an instance to avoid container resolution errors.
                return new RazorpayRouteService('', '');
            }

            return new RazorpayRouteService($keyId, $keySecret);
        });
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
