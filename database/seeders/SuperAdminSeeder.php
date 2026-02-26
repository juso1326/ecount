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
        SuperAdmin::firstOrCreate(
            ['email' => 'admin@ecount.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123456'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ 預設超級管理員建立成功！');
        $this->command->info('Email: admin@ecount.com');
        $this->command->info('Password: admin123456');
    }
}
