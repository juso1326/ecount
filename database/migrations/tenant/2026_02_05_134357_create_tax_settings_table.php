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
        Schema::create('tax_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('稅款名稱（如：營業稅、進項稅）');
            $table->decimal('rate', 5, 2)->comment('稅率（百分比，如：5.00）');
            $table->boolean('is_default')->default(false)->comment('是否為預設稅率');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->text('description')->nullable()->comment('說明');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->timestamps();
            
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_settings');
    }
};
