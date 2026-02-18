<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserBankAccount extends Model
{
    protected $fillable = [
        'user_id',
        'bank_name',
        'bank_branch',
        'bank_account',
        'account_name',
        'is_default',
        'note',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * 當設為預設時，取消其他帳戶的預設狀態
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bankAccount) {
            if ($bankAccount->is_default) {
                static::where('user_id', $bankAccount->user_id)
                    ->where('id', '!=', $bankAccount->id)
                    ->update(['is_default' => false]);
            }
        });
    }

    /**
     * 所屬使用者
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
