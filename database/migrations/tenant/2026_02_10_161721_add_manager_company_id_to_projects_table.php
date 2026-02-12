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
        Schema::table('projects', function (Blueprint $table) {
            // 新增專案負責人（員工公司）欄位
            $table->foreignId('manager_company_id')
                ->nullable()
                ->after('manager_id')
                ->constrained('companies')
                ->onDelete('set null')
                ->comment('專案負責人（員工公司）');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['manager_company_id']);
            $table->dropColumn('manager_company_id');
        });
    }
};
