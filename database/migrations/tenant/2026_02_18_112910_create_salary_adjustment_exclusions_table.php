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
        Schema::create('salary_adjustment_exclusions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('salary_adjustment_id')->constrained()->onDelete('cascade')->comment('加扣項ID');
            $table->integer('year')->comment('年份');
            $table->integer('month')->comment('月份');
            $table->text('reason')->nullable()->comment('排除原因');
            $table->timestamps();
            
            $table->unique(['salary_adjustment_id', 'year', 'month'], 'unique_adjustment_period');
            $table->index(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_adjustment_exclusions');
    }
};
