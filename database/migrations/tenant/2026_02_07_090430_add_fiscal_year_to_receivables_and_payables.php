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
        // 應收帳款增加帳務年度
        Schema::table('receivables', function (Blueprint $table) {
            $table->year('fiscal_year')->nullable()->after('receipt_date')->comment('帳務年度');
            $table->index('fiscal_year');
        });
        
        // 應付帳款增加帳務年度
        Schema::table('payables', function (Blueprint $table) {
            $table->year('fiscal_year')->nullable()->after('payment_date')->comment('帳務年度');
            $table->index('fiscal_year');
        });
        
        // 更新現有資料：根據發票日期設定帳務年度
        DB::statement('UPDATE receivables SET fiscal_year = YEAR(receipt_date) WHERE receipt_date IS NOT NULL');
        DB::statement('UPDATE payables SET fiscal_year = YEAR(payment_date) WHERE payment_date IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropIndex(['fiscal_year']);
            $table->dropColumn('fiscal_year');
        });
        
        Schema::table('payables', function (Blueprint $table) {
            $table->dropIndex(['fiscal_year']);
            $table->dropColumn('fiscal_year');
        });
    }
};
