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
            
            // 部門管理
            'departments.view',
            'departments.create',
            'departments.edit',
            'departments.delete',
            
            // 專案管理
            'projects.view',
            'projects.create',
            'projects.edit',
            'projects.delete',
            
            // 財務管理
            'finance.view',
            'finance.manage',
            
            // 角色權限管理
            'roles.view',
            'roles.manage',
            
            // 系統設定
            'settings.view',
            'settings.manage',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name]);
        }

        // 建立角色並分配權限
        
        // 1. 系統管理員（全部權限）
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        // 2. 經理（大部分權限，不含系統設定）
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'users.view', 'users.create', 'users.edit',
            'companies.view', 'companies.create', 'companies.edit',
            'departments.view', 'departments.create', 'departments.edit', 'departments.delete',
            'projects.view', 'projects.create', 'projects.edit', 'projects.delete',
            'finance.view', 'finance.manage',
            'roles.view',
        ]);

        // 3. 會計（財務相關）
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'companies.view',
            'departments.view',
            'projects.view',
            'finance.view', 'finance.manage',
        ]);

        // 4. 員工（基本檢視權限）
        $employee = Role::firstOrCreate(['name' => 'employee']);
        $employee->givePermissionTo([
            'companies.view',
            'departments.view',
            'projects.view',
            'finance.view',
        ]);

        $this->command->info('✅ 角色和權限建立完成！');
        $this->command->info('   - 系統管理員：' . $admin->permissions->count() . ' 個權限');
        $this->command->info('   - 經理：' . $manager->permissions->count() . ' 個權限');
        $this->command->info('   - 會計：' . $accountant->permissions->count() . ' 個權限');
        $this->command->info('   - 員工：' . $employee->permissions->count() . ' 個權限');
    }
}
