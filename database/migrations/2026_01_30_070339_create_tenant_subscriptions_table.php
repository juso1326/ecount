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
        Schema::create('tenant_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->index()->comment('租戶 ID');
            $table->enum('plan', ['basic', 'professional', 'enterprise'])->comment('方案類型');
            $table->decimal('price', 10, 2)->default(0)->comment('方案價格');
            $table->timestamp('started_at')->comment('開始時間');
            $table->timestamp('ends_at')->comment('結束時間');
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active')->comment('狀態');
            $table->boolean('auto_renew')->default(true)->comment('自動續約');
            $table->text('notes')->nullable()->comment('備註');
            $table->timestamps();
            
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_subscriptions');
    }
};
