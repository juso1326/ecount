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
            // 新增 is_member 欄位
            if (!Schema::hasColumn('companies', 'is_member')) {
                $table->boolean('is_member')->default(false)->after('is_client')->comment('是否為成員');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (Schema::hasColumn('companies', 'is_member')) {
                $table->dropColumn('is_member');
            }
        });
    }
};
