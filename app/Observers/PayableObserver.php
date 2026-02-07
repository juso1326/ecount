<?php

namespace App\Observers;

use App\Models\Payable;
use Carbon\Carbon;

class PayableObserver
{
    /**
     * Handle the Payable "creating" event.
     */
    public function creating(Payable $payable): void
    {
        if ($payable->payment_date && !$payable->fiscal_year) {
            $payable->fiscal_year = Carbon::parse($payable->payment_date)->year;
        }
    }

    /**
     * Handle the Payable "updating" event.
     */
    public function updating(Payable $payable): void
    {
        if ($payable->isDirty('payment_date') && $payable->payment_date) {
            $payable->fiscal_year = Carbon::parse($payable->payment_date)->year;
        }
    }
}
