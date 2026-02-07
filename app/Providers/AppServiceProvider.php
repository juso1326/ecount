<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Receivable;
use App\Models\Payable;
use App\Observers\ReceivableObserver;
use App\Observers\PayableObserver;

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
        Receivable::observe(ReceivableObserver::class);
        Payable::observe(PayableObserver::class);
    }
}
