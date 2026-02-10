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
        Schema::table('project_members', function (Blueprint $table) {
            // 移除舊的外鍵約束
            $table->dropForeign(['user_id']);
            
            // 重命名欄位
            $table->renameColumn('user_id', 'company_id');
            
            // 添加新的外鍵約束
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_members', function (Blueprint $table) {
            // 移除新的外鍵約束
            $table->dropForeign(['company_id']);
            
            // 重命名欄位
            $table->renameColumn('company_id', 'user_id');
            
            // 恢復舊的外鍵約束
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
