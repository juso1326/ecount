<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\Receivable;
use App\Models\Payable;
use App\Observers\ReceivableObserver;
use App\Observers\PayableObserver;
use App\Helpers\CurrencyHelper;

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

        // 註冊貨幣格式化 Blade directive
        Blade::directive('currency', function ($expression) {
            return "<?php echo format_currency($expression); ?>";
        });

        // 註冊 JS 格式化函數 directive
        Blade::directive('currencyJs', function () {
            return "<?php echo CurrencyHelper::jsFormatter(); ?>";
        });
    }
}
