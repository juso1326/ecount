<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('users')->onDelete('cascade')->comment('推薦人');
            $table->foreignId('referred_id')->nullable()->constrained('users')->onDelete('set null')->comment('被推薦人');
            $table->string('referral_code', 20)->unique()->comment('推薦碼');
            $table->string('referred_email')->nullable()->comment('被推薦人Email');
            $table->enum('status', ['pending', 'registered', 'subscribed'])->default('pending')->comment('狀態');
            $table->timestamp('registered_at')->nullable()->comment('註冊時間');
            $table->timestamp('subscribed_at')->nullable()->comment('訂閱時間');
            $table->boolean('reward_given')->default(false)->comment('是否已給予獎勵');
            $table->integer('reward_days')->default(30)->comment('獎勵天數');
            $table->timestamps();
            
            $table->index('referral_code');
            $table->index('referrer_id');
            $table->index(['status', 'reward_given']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');
    }
};
