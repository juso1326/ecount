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
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->comment('標籤類型：project, company, user');
            $table->string('name', 100)->comment('標籤名稱');
            $table->string('color', 7)->default('#3B82F6')->comment('標籤顏色（Hex）');
            $table->text('description')->nullable()->comment('標籤說明');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
        });
        
        // 標籤關聯表（多對多）
        Schema::create('taggables', function (Blueprint $table) {
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->morphs('taggable');
            $table->timestamps();
            
            $table->unique(['tag_id', 'taggable_id', 'taggable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taggables');
        Schema::dropIfExists('tags');
    }
};
