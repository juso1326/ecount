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
        Schema::create('company_attribute_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->comment('公司ID');
            $table->string('attribute_name', 50)->comment('屬性名稱（is_client, is_outsource, is_member）');
            $table->boolean('old_value')->nullable()->comment('舊值');
            $table->boolean('new_value')->comment('新值');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null')->comment('變更人');
            $table->text('note')->nullable()->comment('變更備註');
            $table->timestamp('changed_at')->useCurrent()->comment('變更時間');
            $table->timestamps();
            
            $table->index(['company_id', 'changed_at']);
            $table->index('attribute_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_attribute_histories');
    }
};
