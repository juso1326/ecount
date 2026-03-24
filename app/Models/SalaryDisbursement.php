<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryDisbursement extends Model
{
    protected $fillable = [
        'user_id', 'year', 'month',
        'paid_amount', 'paid_date', 'remark', 'paid_by',
    ];

    protected $casts = [
        'paid_date' => 'date',
        'paid_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
