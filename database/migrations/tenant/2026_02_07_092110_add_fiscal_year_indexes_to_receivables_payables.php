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
        // 應收帳款複合索引
        Schema::table('receivables', function (Blueprint $table) {
            $table->index(['fiscal_year', 'status'], 'idx_receivables_fiscal_status');
            $table->index(['fiscal_year', 'company_id'], 'idx_receivables_fiscal_company');
        });
        
        // 應付帳款複合索引
        Schema::table('payables', function (Blueprint $table) {
            $table->index(['fiscal_year', 'status'], 'idx_payables_fiscal_status');
            $table->index(['fiscal_year', 'payee_type'], 'idx_payables_fiscal_payee');
            $table->index(['fiscal_year', 'company_id'], 'idx_payables_fiscal_company');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropIndex('idx_receivables_fiscal_status');
            $table->dropIndex('idx_receivables_fiscal_company');
        });
        
        Schema::table('payables', function (Blueprint $table) {
            $table->dropIndex('idx_payables_fiscal_status');
            $table->dropIndex('idx_payables_fiscal_payee');
            $table->dropIndex('idx_payables_fiscal_company');
        });
    }
};
