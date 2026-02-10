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
            // 只新增還不存在的欄位
            if (!Schema::hasColumn('companies', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_member')->comment('是否在職');
            }
            if (!Schema::hasColumn('companies', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('is_active')->comment('到職日');
            }
            if (!Schema::hasColumn('companies', 'leave_date')) {
                $table->date('leave_date')->nullable()->after('hire_date')->comment('離職日');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'hire_date', 'leave_date']);
        });
    }
};
