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
            // 新增缺少的欄位
            $table->string('type')->nullable()->after('payment_no')->comment('類型：外製/採購等');
            $table->string('vendor')->nullable()->after('company_id')->comment('對象名稱');
            $table->string('content')->nullable()->after('project_id')->comment('內容說明');
            
            // 金額細分
            $table->decimal('deduction', 15, 2)->default(0)->after('amount')->comment('扣抵金額');
            $table->decimal('net_amount', 15, 2)->default(0)->after('paid_amount')->comment('實際支付金額');
            
            // 發票資訊
            $table->date('invoice_date')->nullable()->after('invoice_no')->comment('發票日期');
            
            // 負責人
            $table->foreignId('responsible_user_id')->nullable()->after('company_id')->constrained('users')->onDelete('set null')->comment('負責人');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payables', function (Blueprint $table) {
            $table->dropForeign(['responsible_user_id']);
            $table->dropColumn([
                'type',
                'vendor',
                'content',
                'deduction',
                'net_amount',
                'invoice_date',
                'responsible_user_id',
            ]);
        });
    }
};
