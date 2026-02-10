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
        Schema::create('company_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->comment('公司ID');
            $table->string('bank_name')->comment('銀行名稱');
            $table->string('branch_name')->nullable()->comment('分行名稱');
            $table->string('account_number')->comment('帳號');
            $table->string('account_name')->nullable()->comment('戶名');
            $table->boolean('is_default')->default(false)->comment('是否為預設');
            $table->text('note')->nullable()->comment('備註');
            $table->timestamps();
            
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_bank_accounts');
    }
};
