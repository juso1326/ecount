<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        'is_outsource',
        'is_client',
        'is_member',
        'is_active',
        'hire_date',
        'leave_date',
        'tax_id',
        'id_number',
        'birth_date',
        'is_tax_entity',
        'invoice_title',
        'invoice_type',
        'representative',
        'contact_person',
        'phone',
        'mobile',
        'emergency_contact',
        'emergency_phone',
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
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_outsource' => 'boolean',
        'is_client' => 'boolean',
        'is_member' => 'boolean',
        'is_tax_entity' => 'boolean',
        'birth_date' => 'date',
        'hire_date' => 'date',
        'leave_date' => 'date',
    ];

    /**
     * 關聯：公司的所有專案
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * 關聯：作為成員參與的專案
     */
    public function memberProjects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
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
     * 關聯：屬性變更歷史
     */
    public function attributeHistories(): HasMany
    {
        return $this->hasMany(CompanyAttributeHistory::class)->orderBy('changed_at', 'desc');
    }

    /**
     * 關聯：銀行帳號
     */
    public function bankAccounts(): HasMany
    {
        return $this->hasMany(CompanyBankAccount::class);
    }

    /**
     * 檢查是否啟用
     */
    public function isActive(): bool
    {
        return $this->is_active;
    }
}
