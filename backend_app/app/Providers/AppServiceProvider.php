<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

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
        // Limit API middleware
        // 60 requests per minute if user is logged.
        // 5 requests per minute if user is not logged. 
        RateLimiter::for('api', function (Request $request) {
            return $request->user() ? Limit::perMinute(60)->by($request->ip()) : Limit::perMinute(5)->by($request->ip());
        });
    }
}
