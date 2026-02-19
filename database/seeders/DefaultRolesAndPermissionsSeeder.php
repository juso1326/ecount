<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DefaultRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // 重置快取
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 定義權限模組
        $modules = [
            'users' => ['view', 'create', 'edit', 'delete'],
            'companies' => ['view', 'create', 'edit', 'delete'],
            'projects' => ['view', 'create', 'edit', 'delete'],
            'receivables' => ['view', 'create', 'edit', 'delete', 'receive'],
            'payables' => ['view', 'create', 'edit', 'delete', 'pay'],
            'salaries' => ['view', 'create', 'edit', 'delete', 'pay'],
            'reports' => ['view', 'financial', 'ar-ap', 'project', 'payroll'],
            'roles' => ['view', 'create', 'edit', 'delete'],
            'settings' => ['view', 'edit'],
            'tags' => ['view', 'create', 'edit', 'delete'],
            'announcements' => ['view', 'edit'],
        ];

        // 創建權限
        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => "{$module}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // 創建系統預設角色
        $this->createSuperAdminRole();
        $this->createFinancialManagerRole();
        $this->createProjectManagerRole();
        $this->createEmployeeRole();
    }

    /**
     * 總管理（所有權限）
     */
    private function createSuperAdminRole()
    {
        $role = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web']
        );
        
        // 授予所有權限
        $role->givePermissionTo(Permission::all());
    }

    /**
     * 財務主管（財務相關權限）
     */
    private function createFinancialManagerRole()
    {
        $role = Role::firstOrCreate(
            ['name' => 'financial_manager', 'guard_name' => 'web']
        );

        $permissions = [
            // 查看權限
            'users.view',
            'companies.view',
            'projects.view',
            
            // 財務完整權限
            'receivables.view', 'receivables.create', 'receivables.edit', 'receivables.delete', 'receivables.receive',
            'payables.view', 'payables.create', 'payables.edit', 'payables.delete', 'payables.pay',
            'salaries.view', 'salaries.create', 'salaries.edit', 'salaries.delete', 'salaries.pay',
            
            // 報表權限
            'reports.view', 'reports.financial', 'reports.ar-ap', 'reports.project', 'reports.payroll',
            
            // 標籤與設定
            'tags.view', 'tags.create', 'tags.edit', 'tags.delete',
            'settings.view',
            'announcements.view', 'announcements.edit',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * 專案經理（專案與應收應付權限）
     */
    private function createProjectManagerRole()
    {
        $role = Role::firstOrCreate(
            ['name' => 'project_manager', 'guard_name' => 'web']
        );

        $permissions = [
            // 查看權限
            'users.view',
            'companies.view', 'companies.create', 'companies.edit',
            
            // 專案完整權限
            'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
            
            // 應收應付編輯權限
            'receivables.view', 'receivables.create', 'receivables.edit', 'receivables.receive',
            'payables.view', 'payables.create', 'payables.edit',
            
            // 薪資查看
            'salaries.view',
            
            // 報表查看
            'reports.view', 'reports.financial', 'reports.ar-ap', 'reports.project',
            
            // 標籤
            'tags.view', 'tags.create', 'tags.edit',
            'announcements.view',
        ];

        $role->syncPermissions($permissions);
    }

    /**
     * 一般員工（基本查看權限）
     */
    private function createEmployeeRole()
    {
        $role = Role::firstOrCreate(
            ['name' => 'employee', 'guard_name' => 'web']
        );

        $permissions = [
            // 基本查看權限
            'users.view',
            'companies.view',
            'projects.view',
            'receivables.view',
            'payables.view',
            'salaries.view', // 只能查看自己的薪資
            'reports.view',
            'tags.view',
            'announcements.view',
        ];

        $role->syncPermissions($permissions);
    }
}
