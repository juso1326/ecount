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
            // 增加稅務相關欄位，與 receivables 對齊
            $table->decimal('amount_before_tax', 15, 2)->default(0)->after('amount')->comment('未稅額');
            $table->boolean('has_tax')->default(true)->after('amount_before_tax')->comment('是否含營業稅');
            $table->decimal('tax_rate', 5, 2)->default(5.00)->after('has_tax')->comment('營業稅率(%)');
            $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_rate')->comment('營業稅額');
            $table->boolean('tax_inclusive')->default(true)->after('tax_amount')->comment('稅金是否內含');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payables', function (Blueprint $table) {
            $table->dropColumn([
                'amount_before_tax',
                'has_tax',
                'tax_rate',
                'tax_amount',
                'tax_inclusive',
            ]);
        });
    }
};
