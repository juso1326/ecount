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
            $table->boolean('is_salary_paid')->default(false)->after('status')->comment('薪資是否已撥款');
            $table->timestamp('salary_paid_at')->nullable()->after('is_salary_paid')->comment('薪資撥款時間');
            $table->decimal('salary_paid_amount', 10, 2)->nullable()->after('salary_paid_at')->comment('實際撥款金額');
            $table->text('salary_paid_remark')->nullable()->after('salary_paid_amount')->comment('撥款備註');
            
            $table->index(['payee_type', 'is_salary_paid'], 'idx_payee_salary_paid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payables', function (Blueprint $table) {
            $table->dropIndex('idx_payee_salary_paid');
            $table->dropColumn(['is_salary_paid', 'salary_paid_at', 'salary_paid_amount', 'salary_paid_remark']);
        });
    }
};
