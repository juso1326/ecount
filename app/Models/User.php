<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'last_login_at',
        'is_active',
        // 員工基本資訊
        'employee_no',
        'short_name',
        'position',
        'supervisor_id',
        // 個人資料
        'id_number',
        'birth_date',
        'phone',
        'mobile',
        'backup_email',
        // 銀行資訊
        'bank_name',
        'bank_branch',
        'bank_account',
        // 緊急聯絡人
        'emergency_contact_name',
        'emergency_contact_phone',
        // 任職資訊
        'hire_date',
        'resign_date',
        'suspend_date',
        // 備註
        'note',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
            'is_active' => 'boolean',
            'birth_date' => 'date',
            'hire_date' => 'date',
            'resign_date' => 'date',
            'suspend_date' => 'date',
        ];
    }

    /**
     * 上層主管關係
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    /**
     * 下屬關係
     */
    public function subordinates()
    {
        return $this->hasMany(User::class, 'supervisor_id');
    }

    /**
     * 參與的專案（多對多關聯）
     */
    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * 關聯：標籤
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * 銀行帳戶（一對多關聯）
     */
    public function bankAccounts()
    {
        return $this->hasMany(UserBankAccount::class);
    }

    /**
     * 取得預設銀行帳戶
     */
    public function defaultBankAccount()
    {
        return $this->hasOne(UserBankAccount::class)->where('is_default', true);
    }
}
