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
        // 標籤支援已通過 taggables 多態表實現
        // Receivable 和 Payable 將使用 MorphToMany 關聯
        // 無需額外的表結構變更
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 無需回滾
    }
};
