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
        // 先檢查哪些欄位存在
        $columnsToCheck = ['id_number', 'birth_date', 'phone', 'mobile', 'bank_name', 'bank_branch', 'bank_account', 'emergency_contact', 'emergency_contact_phone', 'hire_date', 'resign_date'];
        $columnsToRemove = [];
        
        foreach ($columnsToCheck as $column) {
            if (Schema::hasColumn('users', $column)) {
                $columnsToRemove[] = $column;
            }
        }
        
        // 移除存在的欄位
        if (!empty($columnsToRemove)) {
            Schema::table('users', function (Blueprint $table) use ($columnsToRemove) {
                $table->dropColumn($columnsToRemove);
            });
        }
        
        // 新增新欄位
        Schema::table('users', function (Blueprint $table) {
            // 新增關聯到成員（員工）的欄位
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->foreignId('company_id')->nullable()->after('supervisor_id')->comment('關聯的成員（員工）ID');
            }
            
            // 新增權限日期欄位
            if (!Schema::hasColumn('users', 'permission_start_date')) {
                $table->date('permission_start_date')->nullable()->after('is_active')->comment('權限開始日期');
            }
            if (!Schema::hasColumn('users', 'permission_end_date')) {
                $table->date('permission_end_date')->nullable()->after('permission_start_date')->comment('權限結束日期');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 還原欄位
            $table->string('id_number', 20)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_branch', 100)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->string('emergency_contact', 100)->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->date('hire_date')->nullable();
            $table->date('resign_date')->nullable();
            
            // 移除新增的欄位
            $table->dropColumn(['company_id', 'permission_start_date', 'permission_end_date']);
        });
    }
};
