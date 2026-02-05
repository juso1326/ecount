<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->integer('trial_extension_days')->default(0)->after('trial_ends_at')->comment('試用展延天數');
            $table->timestamp('extended_at')->nullable()->after('trial_extension_days')->comment('最後展延時間');
            $table->foreignId('extended_by')->nullable()->after('extended_at')->constrained('users')->nullOnDelete()->comment('展延操作人');
            $table->text('extension_reason')->nullable()->after('extended_by')->comment('展延原因');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['extended_by']);
            $table->dropColumn(['trial_extension_days', 'extended_at', 'extended_by', 'extension_reason']);
        });
    }
};
