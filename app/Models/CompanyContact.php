<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyContact extends Model
{
    protected $fillable = [
        'company_id', 'name', 'phone', 'mobile', 'email', 'note', 'sort_order',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
