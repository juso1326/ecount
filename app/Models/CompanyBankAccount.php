<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyBankAccount extends Model
{
    protected $fillable = [
        'company_id', 'bank_name', 'bank_branch',
        'bank_account', 'bank_account_name', 'note', 'sort_order',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
