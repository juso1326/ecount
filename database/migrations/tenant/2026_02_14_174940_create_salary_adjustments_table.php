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
        Schema::create('salary_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('員工ID');
            $table->enum('type', ['add', 'deduct'])->comment('類型：add=加項, deduct=扣項');
            $table->string('title', 100)->comment('項目名稱');
            $table->decimal('amount', 10, 2)->comment('金額');
            $table->date('start_date')->comment('開始日期');
            $table->date('end_date')->nullable()->comment('結束日期');
            $table->enum('recurrence', ['once', 'monthly', 'yearly'])->default('monthly')->comment('週期：once=單次, monthly=每月, yearly=每年');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->text('remark')->nullable()->comment('備註');
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_adjustments');
    }
};
