<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 清除快取
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // 建立權限
        $permissions = [
            // 使用者管理
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // 公司管理
            'companies.view',
            'companies.create',
            'companies.edit',
            'companies.delete',
            
            // 專案管理
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            
            // 應收帳款
            'receivables.view',
            'receivables.create',
            'receivables.edit',
            'receivables.delete',
            
            // 應付帳款
            'payables.view',
            'payables.create',
            'payables.edit',
            'payables.delete',
            
            // 薪資管理
            'salaries.view',
            'salaries.edit',
            'salaries.pay',
            
            // 財務報表
            'reports.view',
            'reports.export',
            
            // 系統設定
            'settings.view',
            'settings.edit',
            
            // 角色權限管理
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // 建立固定角色方案（is_system = true，不可刪除）
        
        // 1. 總管理（Super Admin）- 所有權限
        $superAdmin = Role::firstOrCreate([
            'name' => '總管理',
            'guard_name' => 'web'
        ]);
        $superAdmin->givePermissionTo(Permission::all());

        // 2. 財務主管（Finance Manager）
        $financeManager = Role::firstOrCreate([
            'name' => '財務主管',
            'guard_name' => 'web'
        ]);
        $financeManager->givePermissionTo([
            'companies.view', 'companies.edit',
            'projects.view',
            'receivables.view', 'receivables.create', 'receivables.edit', 'receivables.delete',
            'payables.view', 'payables.create', 'payables.edit', 'payables.delete',
            'salaries.view', 'salaries.edit', 'salaries.pay',
            'reports.view', 'reports.export',
        ]);

        // 3. 專案經理（Project Manager）
        $projectManager = Role::firstOrCreate([
            'name' => '專案經理',
            'guard_name' => 'web'
        ]);
        $projectManager->givePermissionTo([
            'companies.view', 'companies.create', 'companies.edit',
            'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
            'receivables.view', 'receivables.create', 'receivables.edit',
            'payables.view',
            'reports.view',
        ]);

        // 4. 會計人員（示範角色，可編輯刪除）
        $accountant = Role::firstOrCreate([
            'name' => '會計人員',
            'guard_name' => 'web'
        ]);
        $accountant->givePermissionTo([
            'companies.view',
            'projects.view',
            'receivables.view', 'receivables.create', 'receivables.edit',
            'payables.view', 'payables.create', 'payables.edit',
            'salaries.view', 'salaries.edit',
            'reports.view',
        ]);

        // 5. 一般員工（示範角色，可編輯刪除）
        $employee = Role::firstOrCreate([
            'name' => '一般員工',
            'guard_name' => 'web'
        ]);
        $employee->givePermissionTo([
            'companies.view',
            'projects.view',
            'salaries.view',
        ]);

        $this->command->info('✅ 角色和權限建立完成！');
        $this->command->info('   系統保護角色（不可刪除）：');
        $this->command->info('   - 總管理：' . $superAdmin->permissions->count() . ' 個權限');
        $this->command->info('   - 財務主管：' . $financeManager->permissions->count() . ' 個權限');
        $this->command->info('   - 專案經理：' . $projectManager->permissions->count() . ' 個權限');
        $this->command->info('   示範角色（可編輯刪除）：');
        $this->command->info('   - 會計人員：' . $accountant->permissions->count() . ' 個權限');
        $this->command->info('   - 一般員工：' . $employee->permissions->count() . ' 個權限');
    }
}
