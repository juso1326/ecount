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
            $table->timestamp('plan_started_at')->nullable()->after('plan')->comment('方案開始時間');
            $table->timestamp('plan_ends_at')->nullable()->after('plan_started_at')->comment('方案到期時間');
            $table->boolean('auto_renew')->default(true)->after('plan_ends_at')->comment('自動續約');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['plan_started_at', 'plan_ends_at', 'auto_renew']);
        });
    }
};
