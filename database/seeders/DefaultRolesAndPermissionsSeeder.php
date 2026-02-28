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

        // 預設角色：總管理（不可刪除）+ 3 個預設角色（可刪除）
        $this->createSuperAdminRole();
        $this->createFinancialManagerRole();
        $this->createProjectManagerRole();
        $this->createEmployeeRole();
    }

    /**
     * 總管理（所有權限，不可刪除）
     */
    private function createSuperAdminRole()
    {
        $role = Role::firstOrCreate(
            ['name' => '總管理', 'guard_name' => 'web']
        );
        $role->syncPermissions(Permission::all());
    }

    /**
     * 財務主管（預設角色，可刪除）
     */
    private function createFinancialManagerRole()
    {
        $role = Role::firstOrCreate(
            ['name' => '財務主管', 'guard_name' => 'web']
        );
        $role->syncPermissions([
            'users.view', 'companies.view', 'projects.view',
            'receivables.view', 'receivables.create', 'receivables.edit', 'receivables.delete', 'receivables.receive',
            'payables.view', 'payables.create', 'payables.edit', 'payables.delete', 'payables.pay',
            'salaries.view', 'salaries.create', 'salaries.edit', 'salaries.delete', 'salaries.pay',
            'reports.view', 'reports.financial', 'reports.ar-ap', 'reports.project', 'reports.payroll',
            'tags.view', 'tags.create', 'tags.edit', 'tags.delete',
            'settings.view',
            'announcements.view', 'announcements.edit',
        ]);
    }

    /**
     * 專案經理（預設角色，可刪除）
     */
    private function createProjectManagerRole()
    {
        $role = Role::firstOrCreate(
            ['name' => '專案經理', 'guard_name' => 'web']
        );
        $role->syncPermissions([
            'users.view',
            'companies.view', 'companies.create', 'companies.edit',
            'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
            'receivables.view', 'receivables.create', 'receivables.edit', 'receivables.receive',
            'payables.view', 'payables.create', 'payables.edit',
            'salaries.view',
            'reports.view', 'reports.financial', 'reports.ar-ap', 'reports.project',
            'tags.view', 'tags.create', 'tags.edit',
            'announcements.view',
        ]);
    }

    /**
     * 一般員工（預設角色，可刪除）
     */
    private function createEmployeeRole()
    {
        $role = Role::firstOrCreate(
            ['name' => '一般員工', 'guard_name' => 'web']
        );
        $role->syncPermissions([
            'users.view', 'companies.view', 'projects.view',
            'receivables.view', 'payables.view',
            'salaries.view', 'reports.view', 'tags.view',
            'announcements.view',
        ]);
    }
}
