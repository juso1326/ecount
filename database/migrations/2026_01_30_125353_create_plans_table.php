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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('方案名稱');
            $table->string('slug')->unique()->comment('方案代碼');
            $table->text('description')->nullable()->comment('方案描述');
            $table->decimal('price', 10, 2)->default(0)->comment('月費價格');
            $table->decimal('annual_price', 10, 2)->nullable()->comment('年費價格');
            $table->integer('max_users')->nullable()->comment('最大使用者數');
            $table->integer('max_companies')->nullable()->comment('最大公司數');
            $table->integer('max_projects')->nullable()->comment('最大專案數');
            $table->integer('storage_limit')->nullable()->comment('儲存空間限制(GB)');
            $table->json('features')->nullable()->comment('功能特色');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->boolean('is_featured')->default(false)->comment('是否推薦');
            $table->integer('sort_order')->default(0)->comment('排序順序');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
