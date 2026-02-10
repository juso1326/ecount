<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyBankAccount extends Model
{
    protected $fillable = [
        'company_id',
        'bank_name',
        'branch_name',
        'account_number',
        'account_name',
        'is_default',
        'note',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * 關聯：所屬公司
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
