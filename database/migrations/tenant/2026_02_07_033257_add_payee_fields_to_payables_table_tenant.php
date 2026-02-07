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
            if (!Schema::hasColumn('payables', 'payee_type')) {
                $table->string('payee_type', 20)->nullable()->after('company_id')->comment('給付對象類型：user/company/expense');
            }
            if (!Schema::hasColumn('payables', 'payee_user_id')) {
                $table->foreignId('payee_user_id')->nullable()->after('payee_type')->constrained('users')->onDelete('set null')->comment('給付對象成員');
            }
            if (!Schema::hasColumn('payables', 'payee_company_id')) {
                $table->foreignId('payee_company_id')->nullable()->after('payee_user_id')->constrained('companies')->onDelete('set null')->comment('給付對象廠商');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payables', function (Blueprint $table) {
            if (Schema::hasColumn('payables', 'payee_company_id')) {
                $table->dropForeign(['payee_company_id']);
                $table->dropColumn('payee_company_id');
            }
            if (Schema::hasColumn('payables', 'payee_user_id')) {
                $table->dropForeign(['payee_user_id']);
                $table->dropColumn('payee_user_id');
            }
            if (Schema::hasColumn('payables', 'payee_type')) {
                $table->dropColumn('payee_type');
            }
        });
    }
};
