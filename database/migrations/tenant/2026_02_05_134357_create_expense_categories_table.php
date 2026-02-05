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
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('expense_categories')->onDelete('cascade')->comment('父分類ID');
            $table->string('code', 50)->unique()->comment('支出項目代碼');
            $table->string('name')->comment('支出項目名稱');
            $table->text('description')->nullable()->comment('說明');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->timestamps();
            
            $table->index(['parent_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
    }
};
