<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateTenantJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $tenantId,
        public string $tenantName,
        public string $domain,
        public string $adminEmail,
        public string $adminPassword,
        public string $plan = 'basic',
        public string $billingCycle = 'monthly', // monthly | annual | unlimited
        public ?string $planStartedAt = null,
        public ?string $planEndsAt = null,
        public bool $autoRenew = true,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // 1. 建立租戶記錄（會自動觸發 TenantCreated 事件 → CreateDatabase + MigrateDatabase）
        $startedAt = $this->planStartedAt ? \Carbon\Carbon::parse($this->planStartedAt) : now();

        $endsAt = null;
        if ($this->billingCycle === 'monthly') {
            $endsAt = $startedAt->copy()->addMonth();
        } elseif ($this->billingCycle === 'annual') {
            $endsAt = $startedAt->copy()->addYear();
        }
        // unlimited → endsAt stays null

        $tenant = Tenant::create([
            'id'             => $this->tenantId,
            'name'           => $this->tenantName,
            'email'          => $this->adminEmail,
            'plan'           => $this->plan,
            'plan_started_at'=> $startedAt,
            'plan_ends_at'   => $endsAt,
            'auto_renew'     => $this->autoRenew,
            'status'         => 'active',
        ]);

        // 建立初始方案訂閱記錄
        $tenant->subscriptions()->create([
            'plan'       => $this->plan,
            'started_at' => $startedAt,
            'ends_at'    => $endsAt,
            'status'     => 'active',
            'auto_renew' => $this->autoRenew,
            'notes'      => '初始開通（' . match($this->billingCycle) {
                'monthly'   => '月繳',
                'annual'    => '年繳',
                default     => '無限期',
            } . '）',
        ]);

        // 2. 建立子域名
        $tenant->domains()->create([
            'domain' => $this->domain,
        ]);

        // 3. 在租戶資料庫中建立管理員帳號
        $tenant->run(function () {
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => $this->adminEmail,
                'password' => Hash::make($this->adminPassword),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });
    }
}
