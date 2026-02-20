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
        // 應收帳款增加貨幣欄位
        Schema::table('receivables', function (Blueprint $table) {
            $table->string('currency', 3)->default('TWD')->after('amount')->comment('幣別代碼');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000)->after('currency')->comment('匯率');
        });

        // 應付帳款增加貨幣欄位
        Schema::table('payables', function (Blueprint $table) {
            $table->string('currency', 3)->default('TWD')->after('amount')->comment('幣別代碼');
            $table->decimal('exchange_rate', 10, 4)->default(1.0000)->after('currency')->comment('匯率');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate']);
        });

        Schema::table('payables', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate']);
        });
    }
};
