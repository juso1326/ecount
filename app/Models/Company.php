<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * 公司資料 Model
 */
class Company extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'short_name',
        'type',
        'is_client',
        'is_outsource',
        'tax_id',
        'is_tax_entity',
        'invoice_title',
        'invoice_type',
        'representative',
        'contact_person',
        'phone',
        'mobile',
        'fax',
        'email',
        'address',
        'website',
        'facebook',
        'line_id',
        'instagram',
        'business_hours',
        'industry',
        'capital',
        'bank_name',
        'bank_branch',
        'bank_account',
        'bank_account_name',
        'logo_path',
        'brand_color',
        'note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_client' => 'boolean',
        'is_outsource' => 'boolean',
        'is_tax_entity' => 'boolean',
    ];

    public function contacts(): HasMany
    {
        return $this->hasMany(CompanyContact::class)->orderBy('sort_order');
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(CompanyBankAccount::class)->orderBy('sort_order');
    }

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
     * 關聯：標籤
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * 檢查是否啟用
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
