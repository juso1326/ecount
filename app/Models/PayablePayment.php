<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 應付給付記錄 Model (薪資入帳)
 */
class PayablePayment extends Model
{
    protected $fillable = [
        'payable_id',
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
     * 關聯：所屬應付帳款
     */
    public function payable(): BelongsTo
    {
        return $this->belongsTo(Payable::class);
    }
}
