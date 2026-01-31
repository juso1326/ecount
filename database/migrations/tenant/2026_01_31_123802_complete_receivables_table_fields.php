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
        Schema::table('receivables', function (Blueprint $table) {
            // 重命名和新增欄位以符合實際業務需求
            $table->date('issue_date')->nullable()->after('receipt_date')->comment('開立日');
            $table->string('content')->nullable()->after('project_id')->comment('內容說明');
            $table->string('quote_no')->nullable()->after('content')->comment('報價單號');
            
            // 金額細分
            $table->decimal('amount_before_tax', 15, 2)->default(0)->after('amount')->comment('未稅額');
            $table->boolean('has_tax')->default(true)->after('amount_before_tax')->comment('是否含營業稅');
            $table->decimal('tax_rate', 5, 2)->default(5.00)->after('has_tax')->comment('營業稅率(%)');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate')->comment('營業稅額');
            $table->decimal('withholding_tax', 15, 2)->default(0)->after('received_amount')->comment('扣繳稅額');
            $table->decimal('net_amount', 15, 2)->default(0)->after('withholding_tax')->comment('實際入帳金額');
            
            // 負責人
            $table->foreignId('responsible_user_id')->nullable()->after('company_id')->constrained('users')->onDelete('set null')->comment('負責人');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receivables', function (Blueprint $table) {
            $table->dropForeign(['responsible_user_id']);
            $table->dropColumn([
                'issue_date',
                'content',
                'quote_no',
                'amount_before_tax',
                'has_tax',
                'tax_rate',
                'tax_amount',
                'withholding_tax',
                'net_amount',
                'responsible_user_id',
            ]);
        });
    }
};
