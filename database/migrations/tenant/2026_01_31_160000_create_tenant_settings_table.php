<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenant_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, number, boolean, json
            $table->string('group')->default('general'); // general, company, system, account
            $table->string('label');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 插入預設設定
        DB::table('tenant_settings')->insert([
            [
                'key' => 'company_code_prefix',
                'value' => 'C',
                'type' => 'string',
                'group' => 'company',
                'label' => '公司代碼前綴',
                'description' => '新增公司時自動產生的代碼前綴',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_code_length',
                'value' => '4',
                'type' => 'number',
                'group' => 'company',
                'label' => '公司代碼長度',
                'description' => '公司代碼數字部分的長度',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_code_auto',
                'value' => 'true',
                'type' => 'boolean',
                'group' => 'company',
                'label' => '自動產生公司代碼',
                'description' => '是否自動產生公司代碼（關閉則需手動輸入）',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_settings');
    }
};
