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
        Schema::table('users', function (Blueprint $table) {
            // 基本資訊
            $table->string('employee_no')->nullable()->after('id')->comment('員工編號');
            $table->string('short_name')->nullable()->after('name')->comment('簡稱/英文名');
            $table->string('id_number')->nullable()->after('email')->comment('身分證字號');
            $table->date('birth_date')->nullable()->after('id_number')->comment('出生年月日');
            
            // 聯絡資訊
            $table->string('phone')->nullable()->after('email')->comment('市話');
            $table->string('mobile')->nullable()->after('phone')->comment('手機');
            $table->string('backup_email')->nullable()->after('email')->comment('備份 Email');
            
            // 職位資訊
            $table->string('position')->nullable()->after('short_name')->comment('職位');
            $table->foreignId('department_id')->nullable()->after('position')->constrained()->onDelete('set null')->comment('部門 ID');
            
            // 銀行資訊
            $table->string('bank_name')->nullable()->after('mobile')->comment('銀行名稱');
            $table->string('bank_branch')->nullable()->after('bank_name')->comment('分行');
            $table->string('bank_account')->nullable()->after('bank_branch')->comment('帳號');
            
            // 緊急聯絡人
            $table->string('emergency_contact_name')->nullable()->after('bank_account')->comment('緊急聯絡人');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name')->comment('緊急聯絡電話');
            
            // 在職狀態
            $table->date('hire_date')->nullable()->after('emergency_contact_phone')->comment('到職日');
            $table->date('resign_date')->nullable()->after('hire_date')->comment('離職日');
            $table->date('suspend_date')->nullable()->after('resign_date')->comment('停權日');
            
            // 其他
            $table->text('note')->nullable()->after('is_active')->comment('備註');
            $table->json('settings')->nullable()->after('note')->comment('個人設定（JSON）');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'employee_no',
                'short_name',
                'id_number',
                'birth_date',
                'phone',
                'mobile',
                'backup_email',
                'position',
                'department_id',
                'bank_name',
                'bank_branch',
                'bank_account',
                'emergency_contact_name',
                'emergency_contact_phone',
                'hire_date',
                'resign_date',
                'suspend_date',
                'note',
                'settings',
            ]);
        });
    }
};
