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
        Schema::create('user_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bank_name', 100)->comment('銀行名稱');
            $table->string('bank_branch', 100)->nullable()->comment('分行名稱');
            $table->string('bank_account', 50)->comment('帳號');
            $table->string('account_name', 100)->nullable()->comment('戶名');
            $table->boolean('is_default')->default(false)->comment('是否為預設');
            $table->string('note')->nullable()->comment('備註');
            $table->timestamps();
            
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_bank_accounts');
    }
};
