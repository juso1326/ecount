<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'bank_name',
        'bank_branch',
        'bank_account',
        'account_name',
        'is_default',
        'is_active',
        'note',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bankAccount) {
            if ($bankAccount->is_default) {
                static::where('id', '!=', $bankAccount->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}
