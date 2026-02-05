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
        Schema::table('payables', function (Blueprint $table) {
            $table->string('payee_type', 20)->nullable()->after('company_id')->comment('給付對象類型：member/vendor/expense');
            $table->foreignId('payee_user_id')->nullable()->after('payee_type')->constrained('users')->onDelete('set null')->comment('給付對象成員');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payables', function (Blueprint $table) {
            $table->dropForeign(['payee_user_id']);
            $table->dropColumn(['payee_type', 'payee_user_id']);
        });
    }
};
