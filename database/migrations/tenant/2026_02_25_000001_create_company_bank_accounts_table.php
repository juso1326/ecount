<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_branch', 100)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->string('bank_account_name', 100)->nullable();
            $table->string('note', 255)->nullable();
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_bank_accounts');
    }
};
