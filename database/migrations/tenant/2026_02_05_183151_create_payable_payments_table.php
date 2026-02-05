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
        Schema::create('payable_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payable_id')->constrained()->onDelete('cascade')->comment('應付帳款ID');
            $table->date('payment_date')->comment('給付日期');
            $table->decimal('amount', 15, 2)->comment('給付金額');
            $table->string('payment_method', 50)->nullable()->comment('給付方式');
            $table->text('note')->nullable()->comment('備註');
            $table->timestamps();
            
            $table->index('payable_id');
            $table->index('payment_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payable_payments');
    }
};
