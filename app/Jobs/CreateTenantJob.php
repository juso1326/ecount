<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

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
        $tenant = null;

        try {
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

            // 3. 在租戶資料庫中建立管理員帳號 + 角色
            $tenant->run(function () {
                // 建立 admin 使用者
                $userId = DB::table('users')->insertGetId([
                    'name'               => 'Admin',
                    'email'              => $this->adminEmail,
                    'password'           => Hash::make($this->adminPassword),
                    'email_verified_at'  => now(),
                    'is_active'          => true,
                    'created_at'         => now(),
                    'updated_at'         => now(),
                ]);

                // 初始化權限與角色
                $this->seedRolesAndPermissions();

                // 指派 admin 角色
                $adminUser = \App\Models\User::find($userId);
                if ($adminUser) {
                    $adminUser->assignRole('admin');
                }
            });

        } catch (\Throwable $e) {
            Log::error("CreateTenantJob 失敗，開始回滾租戶 [{$this->tenantId}]", [
                'error' => $e->getMessage(),
            ]);

            $this->rollback($tenant);

            throw $e;
        }
    }

    /**
     * 建立失敗時回滾：刪除租戶資料庫、domain 記錄、tenant 記錄
     */
    private function rollback(?Tenant $tenant): void
    {
        if (! $tenant) {
            // tenant 記錄本身都還沒建立，嘗試查一次再刪
            $tenant = Tenant::find($this->tenantId);
        }

        if (! $tenant) {
            return;
        }

        try {
            // 刪除租戶資料庫（Spatie Tenancy 的 DeleteDatabase 事件會在 $tenant->delete() 時觸發）
            $tenant->delete();
            Log::info("回滾成功：租戶 [{$this->tenantId}] 已清除");
        } catch (\Throwable $rollbackEx) {
            Log::error("回滾失敗：租戶 [{$this->tenantId}]", [
                'error' => $rollbackEx->getMessage(),
            ]);
            // 嘗試直接 DROP 資料庫
            try {
                $dbName = 'tenant_' . $this->tenantId . '_db';
                DB::statement("DROP DATABASE IF EXISTS `{$dbName}`");
                $tenant->domains()->delete();
                $tenant->forceDelete();
                Log::info("強制清除資料庫 [{$dbName}] 成功");
            } catch (\Throwable $forceEx) {
                Log::error("強制清除失敗", ['error' => $forceEx->getMessage()]);
            }
        }
    }

    /** 供外部呼叫（如重建資料庫）直接在當前 DB context 執行 seed */
    public function runSeedOnly(): void
    {
        $this->seedRolesAndPermissions();
    }

    private function seedRolesAndPermissions(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modules = [
            'users'         => ['view', 'create', 'edit', 'delete'],
            'companies'     => ['view', 'create', 'edit', 'delete'],
            'projects'      => ['view', 'create', 'edit', 'delete'],
            'receivables'   => ['view', 'create', 'edit', 'delete', 'receive'],
            'payables'      => ['view', 'create', 'edit', 'delete', 'pay'],
            'salaries'      => ['view', 'create', 'edit', 'delete', 'pay'],
            'reports'       => ['view', 'financial', 'ar-ap', 'project', 'payroll'],
            'roles'         => ['view', 'create', 'edit', 'delete'],
            'settings'      => ['view', 'edit'],
            'tags'          => ['view', 'create', 'edit', 'delete'],
            'announcements' => ['view', 'edit'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$module}.{$action}", 'guard_name' => 'web']);
            }
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo([
            'users.view', 'companies.view', 'projects.view', 'projects.create', 'projects.edit',
            'receivables.view', 'receivables.create', 'receivables.edit',
            'payables.view', 'payables.create', 'payables.edit',
            'salaries.view', 'reports.view', 'reports.financial', 'reports.ar-ap', 'reports.project', 'reports.payroll',
        ]);

        $accountantRole = Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        $accountantRole->givePermissionTo([
            'companies.view', 'projects.view',
            'receivables.view', 'receivables.create', 'receivables.edit', 'receivables.receive',
            'payables.view', 'payables.create', 'payables.edit', 'payables.pay',
            'salaries.view', 'reports.view', 'reports.financial', 'reports.ar-ap', 'reports.payroll',
        ]);

        $employeeRole = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);
        $employeeRole->givePermissionTo(['companies.view', 'projects.view', 'receivables.view', 'payables.view', 'reports.view']);
    }
}
