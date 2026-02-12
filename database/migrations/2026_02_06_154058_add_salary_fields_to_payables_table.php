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
        // Payables table is in tenant database, skip if not multi-tenant context
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is handled in tenant migrations
    }
};
