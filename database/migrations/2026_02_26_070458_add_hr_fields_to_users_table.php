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
            if (!Schema::hasColumn('users', 'employee_no'))        $table->string('employee_no', 50)->nullable()->after('password');
            if (!Schema::hasColumn('users', 'short_name'))         $table->string('short_name', 50)->nullable()->after('employee_no');
            if (!Schema::hasColumn('users', 'position'))           $table->string('position', 100)->nullable()->after('short_name');
            if (!Schema::hasColumn('users', 'supervisor_id'))      $table->unsignedBigInteger('supervisor_id')->nullable()->after('position');
            if (!Schema::hasColumn('users', 'id_number'))          $table->string('id_number', 20)->nullable()->after('supervisor_id');
            if (!Schema::hasColumn('users', 'birth_date'))         $table->date('birth_date')->nullable()->after('id_number');
            if (!Schema::hasColumn('users', 'phone'))              $table->string('phone', 30)->nullable()->after('birth_date');
            if (!Schema::hasColumn('users', 'mobile'))             $table->string('mobile', 30)->nullable()->after('phone');
            if (!Schema::hasColumn('users', 'backup_email'))       $table->string('backup_email')->nullable()->after('mobile');
            if (!Schema::hasColumn('users', 'bank_name'))          $table->string('bank_name', 100)->nullable()->after('backup_email');
            if (!Schema::hasColumn('users', 'bank_branch'))        $table->string('bank_branch', 100)->nullable()->after('bank_name');
            if (!Schema::hasColumn('users', 'bank_account'))       $table->string('bank_account', 50)->nullable()->after('bank_branch');
            if (!Schema::hasColumn('users', 'emergency_contact_name'))  $table->string('emergency_contact_name', 100)->nullable()->after('bank_account');
            if (!Schema::hasColumn('users', 'emergency_contact_phone')) $table->string('emergency_contact_phone', 30)->nullable()->after('emergency_contact_name');
            if (!Schema::hasColumn('users', 'hire_date'))          $table->date('hire_date')->nullable()->after('emergency_contact_phone');
            if (!Schema::hasColumn('users', 'resign_date'))        $table->date('resign_date')->nullable()->after('hire_date');
            if (!Schema::hasColumn('users', 'suspend_date'))       $table->date('suspend_date')->nullable()->after('resign_date');
            if (!Schema::hasColumn('users', 'note'))               $table->text('note')->nullable()->after('suspend_date');
            if (!Schema::hasColumn('users', 'last_login_at'))      $table->timestamp('last_login_at')->nullable()->after('note');
            if (!Schema::hasColumn('users', 'is_active'))          $table->boolean('is_active')->default(true)->after('last_login_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['employee_no','short_name','position','supervisor_id','id_number','birth_date',
                     'phone','mobile','backup_email','bank_name','bank_branch','bank_account',
                     'emergency_contact_name','emergency_contact_phone','hire_date','resign_date',
                     'suspend_date','note','last_login_at','is_active'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('users', $col)) $table->dropColumn($col);
            }
        });
    }
};
