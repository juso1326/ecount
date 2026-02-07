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
        if (Schema::connection('central')->hasTable('super_admins')) {
            return;
        }
        
        Schema::connection('central')->create('super_admins', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 超級管理員名稱
            $table->string('email')->unique(); // 信箱（登入帳號）
            $table->string('password'); // 密碼
            $table->boolean('is_active')->default(true); // 是否啟用
            $table->timestamp('email_verified_at')->nullable(); // Email 驗證時間
            $table->rememberToken(); // 記住我 Token
            $table->timestamp('last_login_at')->nullable(); // 最後登入時間
            $table->string('last_login_ip')->nullable(); // 最後登入 IP
            $table->timestamps();
            $table->softDeletes(); // 軟刪除
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('central')->dropIfExists('super_admins');
    }
};
