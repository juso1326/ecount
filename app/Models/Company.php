<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 公司資料 Model
 */
class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'tax_id',
        'representative',
        'phone',
        'fax',
        'email',
        'address',
        'website',
        'note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * 關聯：公司的所有專案
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * 關聯：公司的應收帳款
     */
    public function receivables(): HasMany
    {
        return $this->hasMany(Receivable::class);
    }

    /**
     * 關聯：公司的應付帳款
     */
    public function payables(): HasMany
    {
        return $this->hasMany(Payable::class);
    }

    /**
     * 檢查是否啟用
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
