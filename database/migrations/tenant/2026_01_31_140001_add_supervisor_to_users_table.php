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
        // 檢查 department_id 是否存在
        $hasDepartmentId = Schema::hasColumn('users', 'department_id');
        
        Schema::table('users', function (Blueprint $table) use ($hasDepartmentId) {
            // 如果 department_id 不存在，先創建它
            if (!$hasDepartmentId) {
                $table->foreignId('department_id')->nullable()->after('email')->comment('所屬部門');
            }
        });
        
        // 新增 supervisor_id
        if (!Schema::hasColumn('users', 'supervisor_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('supervisor_id')->nullable()->after('department_id')->constrained('users')->nullOnDelete()->comment('上層主管');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'supervisor_id')) {
                $table->dropForeign(['supervisor_id']);
                $table->dropColumn('supervisor_id');
            }
        });
    }
};
