<?php

namespace Database\Seeders;

use App\Models\SuperAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 建立預設超級管理員
        SuperAdmin::create([
            'name' => 'Super Admin',
            'email' => 'admin@ecount.com',
            'password' => Hash::make('admin123456'), // 預設密碼，生產環境請修改
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('✅ 預設超級管理員建立成功！');
        $this->command->info('Email: admin@ecount.com');
        $this->command->info('Password: admin123456');
    }
}
