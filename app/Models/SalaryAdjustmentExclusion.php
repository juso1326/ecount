<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryAdjustmentExclusion extends Model
{
    protected $fillable = [
        'salary_adjustment_id',
        'year',
        'month',
        'reason',
    ];

    public function salaryAdjustment(): BelongsTo
    {
        return $this->belongsTo(SalaryAdjustment::class);
    }
}
