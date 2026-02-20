<?php

namespace App\Models;

use App\Traits\HandlesEmptyDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceivablePayment extends Model
{
    use HandlesEmptyDates;
    protected $fillable = [
        'receivable_id',
        'payment_date',
        'amount',
        'payment_method',
        'note',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * 關聯：應收帳款
     */
    public function receivable(): BelongsTo
    {
        return $this->belongsTo(Receivable::class);
    }
}
