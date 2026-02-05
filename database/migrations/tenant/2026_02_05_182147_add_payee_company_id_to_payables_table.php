<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payables', function (Blueprint $table) {
            $table->foreignId('payee_company_id')->nullable()->after('payee_user_id')->constrained('companies')->onDelete('set null')->comment('給付對象廠商');
        });
    }

    public function down(): void
    {
        Schema::table('payables', function (Blueprint $table) {
            $table->dropForeign(['payee_company_id']);
            $table->dropColumn('payee_company_id');
        });
    }
};
