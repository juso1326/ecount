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
        // 1. 程式碼表（分類碼系統）- 用於各種下拉選單
        Schema::create('codes', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50)->comment('分類（如：department_type, project_status）');
            $table->string('code', 50)->comment('代碼');
            $table->string('name')->comment('名稱');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->text('description')->nullable()->comment('說明');
            $table->timestamps();
            
            $table->unique(['category', 'code']);
            $table->index('category');
        });

        // 2. 公司資料表
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('公司代碼');
            $table->string('name')->comment('公司名稱');
            $table->string('tax_id', 20)->nullable()->comment('統一編號');
            $table->string('representative')->nullable()->comment('負責人');
            $table->string('phone', 20)->nullable()->comment('電話');
            $table->string('fax', 20)->nullable()->comment('傳真');
            $table->string('email')->nullable()->comment('Email');
            $table->string('address')->nullable()->comment('地址');
            $table->string('website')->nullable()->comment('網址');
            $table->text('note')->nullable()->comment('備註');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. 部門資料表
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('部門代碼');
            $table->string('name')->comment('部門名稱');
            $table->foreignId('parent_id')->nullable()->constrained('departments')->onDelete('set null')->comment('上層部門');
            $table->string('type', 50)->nullable()->comment('部門類型（關聯 codes）');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null')->comment('部門主管');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->text('note')->nullable()->comment('備註');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->timestamps();
            $table->softDeletes();
        });

        // 4. 專案資料表
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->comment('專案代碼');
            $table->string('name')->comment('專案名稱');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->comment('所屬公司');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null')->comment('負責部門');
            $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null')->comment('專案經理');
            $table->string('status', 50)->default('planning')->comment('專案狀態');
            $table->date('start_date')->nullable()->comment('開始日期');
            $table->date('end_date')->nullable()->comment('結束日期');
            $table->decimal('budget', 15, 2)->default(0)->comment('預算金額');
            $table->decimal('actual_cost', 15, 2)->default(0)->comment('實際成本');
            $table->text('description')->nullable()->comment('專案描述');
            $table->text('note')->nullable()->comment('備註');
            $table->boolean('is_active')->default(true)->comment('是否啟用');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['company_id', 'status']);
            $table->index('start_date');
        });

        // 5. 應收帳款表
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no', 50)->unique()->comment('收據編號');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade')->comment('所屬專案');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->comment('客戶公司');
            $table->date('receipt_date')->comment('收據日期');
            $table->date('due_date')->nullable()->comment('到期日');
            $table->decimal('amount', 15, 2)->comment('應收金額');
            $table->decimal('received_amount', 15, 2)->default(0)->comment('已收金額');
            $table->decimal('remaining_amount', 15, 2)->comment('未收金額');
            $table->string('status', 50)->default('unpaid')->comment('付款狀態');
            $table->string('payment_method', 50)->nullable()->comment('付款方式');
            $table->date('paid_date')->nullable()->comment('實際收款日期');
            $table->string('invoice_no', 50)->nullable()->comment('發票號碼');
            $table->text('note')->nullable()->comment('備註');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['project_id', 'status']);
            $table->index('receipt_date');
            $table->index('due_date');
        });

        // 6. 應付帳款表
        Schema::create('payables', function (Blueprint $table) {
            $table->id();
            $table->string('payment_no', 50)->unique()->comment('付款單號');
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade')->comment('所屬專案');
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->comment('供應商公司');
            $table->date('payment_date')->comment('付款日期');
            $table->date('due_date')->nullable()->comment('到期日');
            $table->decimal('amount', 15, 2)->comment('應付金額');
            $table->decimal('paid_amount', 15, 2)->default(0)->comment('已付金額');
            $table->decimal('remaining_amount', 15, 2)->comment('未付金額');
            $table->string('status', 50)->default('unpaid')->comment('付款狀態');
            $table->string('payment_method', 50)->nullable()->comment('付款方式');
            $table->date('paid_date')->nullable()->comment('實際付款日期');
            $table->string('invoice_no', 50)->nullable()->comment('發票號碼');
            $table->text('note')->nullable()->comment('備註');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['project_id', 'status']);
            $table->index('payment_date');
            $table->index('due_date');
        });

        // 7. 專案成員表（多對多關係）
        Schema::create('project_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('role', 50)->nullable()->comment('角色');
            $table->date('joined_at')->nullable()->comment('加入日期');
            $table->timestamps();
            
            $table->unique(['project_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_members');
        Schema::dropIfExists('payables');
        Schema::dropIfExists('receivables');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('codes');
    }
};
