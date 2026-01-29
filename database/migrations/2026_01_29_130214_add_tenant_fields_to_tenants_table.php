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
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('name')->after('id')->comment('商家名稱');
            $table->string('email')->after('name')->nullable()->comment('聯絡信箱');
            $table->string('plan')->default('basic')->after('email')->comment('訂閱方案: basic, professional, enterprise');
            $table->string('status')->default('active')->after('plan')->comment('狀態: active, suspended, inactive');
            $table->json('settings')->nullable()->after('status')->comment('商家設定');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['name', 'email', 'plan', 'status', 'settings']);
        });
    }
};
