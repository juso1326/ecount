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
            // 新增缺少的欄位
            $table->string('project_type')->nullable()->after('name')->comment('專案類型');
            $table->string('content')->nullable()->after('description')->comment('專案內容摘要');
            $table->string('quote_no')->nullable()->after('code')->comment('報價單號');
            $table->json('members')->nullable()->after('manager_id')->comment('專案成員（JSON）');
            $table->foreignId('responsible_user_id')->nullable()->after('manager_id')->constrained('users')->onDelete('set null')->comment('負責人');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['responsible_user_id']);
            $table->dropColumn([
                'project_type',
                'content',
                'quote_no',
                'members',
                'responsible_user_id',
            ]);
        });
    }
};
