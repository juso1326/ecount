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
        Schema::table('companies', function (Blueprint $table) {
            // Check and add columns only if they don't exist
            if (!Schema::hasColumn('companies', 'id_number')) {
                $table->string('id_number', 20)->nullable()->after('tax_id')->comment('身分證字號');
            }
            if (!Schema::hasColumn('companies', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('id_number')->comment('出生年月日');
            }
            if (!Schema::hasColumn('companies', 'mobile')) {
                $table->string('mobile', 20)->nullable()->after('phone')->comment('手機');
            }
            if (!Schema::hasColumn('companies', 'emergency_contact')) {
                $table->string('emergency_contact', 100)->nullable()->after('mobile')->comment('緊急聯絡人姓名');
            }
            if (!Schema::hasColumn('companies', 'emergency_phone')) {
                $table->string('emergency_phone', 20)->nullable()->after('emergency_contact')->comment('緊急聯絡電話');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'id_number',
                'birth_date',
                'mobile',
                'emergency_contact',
                'emergency_phone',
            ]);
        });
    }
};
