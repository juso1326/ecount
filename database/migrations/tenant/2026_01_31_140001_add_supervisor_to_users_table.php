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
        Schema::table('users', function (Blueprint $table) {
            // 新增上層主管欄位
            if (!Schema::hasColumn('users', 'supervisor_id')) {
                $table->foreignId('supervisor_id')->nullable()->after('department_id')->constrained('users')->nullOnDelete()->comment('上層主管');
            }
            
            // 重新命名欄位（如果需要）
            if (Schema::hasColumn('users', 'emergency_contact_name') && !Schema::hasColumn('users', 'emergency_contact')) {
                $table->renameColumn('emergency_contact_name', 'emergency_contact');
            }
            if (Schema::hasColumn('users', 'suspend_date') && !Schema::hasColumn('users', 'suspended_at')) {
                $table->renameColumn('suspend_date', 'suspended_at');
            }
        });
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
