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
        Schema::table('payables', function (Blueprint $table) {
            $table->string('expense_company_name', 100)->nullable()->after('payee_company_id')->comment('採購-公司名稱（自由輸入）');
            $table->string('expense_tax_id', 20)->nullable()->after('expense_company_name')->comment('採購-統一編號');
            $table->unsignedBigInteger('advance_user_id')->nullable()->after('expense_tax_id')->comment('代墊成員 user_id');
            $table->foreign('advance_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payables', function (Blueprint $table) {
            $table->dropForeign(['advance_user_id']);
            $table->dropColumn(['expense_company_name', 'expense_tax_id', 'advance_user_id']);
        });
    }
};
