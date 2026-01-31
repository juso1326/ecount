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
        Schema::table('companies', function (Blueprint $table) {
            // 新增 is_client 欄位（對應舊系統 comm01_type3）
            if (!Schema::hasColumn('companies', 'is_client')) {
                $table->boolean('is_client')->default(false)->after('is_outsource')->comment('是否為客戶');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'is_client')) {
                $table->dropColumn('is_client');
            }
        });
    }
};
