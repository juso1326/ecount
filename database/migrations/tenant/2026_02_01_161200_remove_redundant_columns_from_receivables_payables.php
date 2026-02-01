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
        // 刪除 receivables 的冗餘欄位
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropColumn([
                'remaining_amount',  // 可計算: amount - received_amount
                'net_amount',        // 可計算: received_amount - withholding_tax
                'issue_date',        // 與 receipt_date 重複
                'has_tax',           // 可用 tax_amount > 0 判斷
            ]);
        });

        // 刪除 payables 的冗餘欄位
        Schema::table('payables', function (Blueprint $table) {
            $table->dropColumn([
                'remaining_amount',  // 可計算: amount - paid_amount
                'net_amount',        // 可計算: paid_amount - deduction
                'vendor',            // 已有 company_id 關聯
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 恢復 receivables 欄位
        Schema::table('receivables', function (Blueprint $table) {
            $table->decimal('remaining_amount', 15, 2)->after('received_amount')->comment('未收金額');
            $table->decimal('net_amount', 15, 2)->default(0)->after('withholding_tax')->comment('實際入帳金額');
            $table->date('issue_date')->nullable()->after('receipt_date')->comment('開立日');
            $table->boolean('has_tax')->default(true)->after('amount_before_tax')->comment('是否含營業稅');
        });

        // 恢復 payables 欄位
        Schema::table('payables', function (Blueprint $table) {
            $table->decimal('remaining_amount', 15, 2)->after('paid_amount')->comment('未付金額');
            $table->decimal('net_amount', 15, 2)->default(0)->after('paid_amount')->comment('實際支付金額');
            $table->string('vendor')->nullable()->after('company_id')->comment('對象名稱');
        });
    }
};
