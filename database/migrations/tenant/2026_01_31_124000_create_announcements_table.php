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
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->text('content')->nullable()->comment('公告內容');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null')->comment('建立者');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null')->comment('更新者');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
