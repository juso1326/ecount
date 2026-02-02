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
        Schema::create('receivable_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('receivable_id')->constrained('receivables')->onDelete('cascade')->comment('應收帳款ID');
            $table->date('payment_date')->comment('收款日期');
            $table->decimal('amount', 15, 2)->comment('收款金額');
            $table->string('payment_method', 50)->nullable()->comment('收款方式');
            $table->text('note')->nullable()->comment('備註');
            $table->timestamps();
            
            $table->index(['receivable_id', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receivable_payments');
    }
};
