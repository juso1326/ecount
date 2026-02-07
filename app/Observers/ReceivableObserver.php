<?php

namespace App\Observers;

use App\Models\Receivable;
use Carbon\Carbon;

class ReceivableObserver
{
    /**
     * Handle the Receivable "creating" event.
     */
    public function creating(Receivable $receivable): void
    {
        if ($receivable->receipt_date && !$receivable->fiscal_year) {
            $receivable->fiscal_year = Carbon::parse($receivable->receipt_date)->year;
        }
    }

    /**
     * Handle the Receivable "updating" event.
     */
    public function updating(Receivable $receivable): void
    {
        if ($receivable->isDirty('receipt_date') && $receivable->receipt_date) {
            $receivable->fiscal_year = Carbon::parse($receivable->receipt_date)->year;
        }
    }
}
