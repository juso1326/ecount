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
            // 基本資訊補充
            $table->string('short_name', 50)->nullable()->after('name')->comment('簡稱');
            $table->enum('type', ['company', 'individual'])->default('company')->after('short_name')->comment('類型：公司/個人');
            $table->boolean('is_outsource')->default(false)->after('type')->comment('是否為外製');
            
            // Logo 與品牌
            $table->string('logo_path')->nullable()->after('is_outsource')->comment('Logo 路徑');
            $table->string('brand_color', 7)->nullable()->after('logo_path')->comment('品牌色碼');
            
            // 聯絡資訊補充
            $table->string('contact_person')->nullable()->after('representative')->comment('聯絡人');
            $table->string('mobile', 20)->nullable()->after('phone')->comment('手機');
            
            // 營業資訊
            $table->string('business_hours')->nullable()->after('website')->comment('營業時間');
            $table->string('industry')->nullable()->after('business_hours')->comment('產業別');
            $table->string('capital')->nullable()->after('industry')->comment('資本額');
            
            // 銀行資訊
            $table->string('bank_name')->nullable()->after('capital')->comment('銀行名稱');
            $table->string('bank_branch')->nullable()->after('bank_name')->comment('分行名稱');
            $table->string('bank_account')->nullable()->after('bank_branch')->comment('銀行帳號');
            $table->string('bank_account_name')->nullable()->after('bank_account')->comment('戶名');
            
            // 稅務資訊
            $table->boolean('is_tax_entity')->default(true)->after('tax_id')->comment('是否為課稅主體');
            $table->string('invoice_title')->nullable()->after('is_tax_entity')->comment('發票抬頭');
            $table->enum('invoice_type', ['duplicate', 'triplicate'])->default('duplicate')->after('invoice_title')->comment('發票類型：二聯式/三聯式');
            
            // 社群媒體
            $table->string('facebook')->nullable()->after('website')->comment('Facebook');
            $table->string('line_id')->nullable()->after('facebook')->comment('LINE ID');
            $table->string('instagram')->nullable()->after('line_id')->comment('Instagram');
            
            // 索引
            $table->index('type');
            $table->index(['is_active', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['type']);
            $table->dropIndex(['is_active', 'type']);
            
            $table->dropColumn([
                'short_name',
                'type',
                'is_outsource',
                'logo_path',
                'brand_color',
                'contact_person',
                'mobile',
                'business_hours',
                'industry',
                'capital',
                'bank_name',
                'bank_branch',
                'bank_account',
                'bank_account_name',
                'is_tax_entity',
                'invoice_title',
                'invoice_type',
                'facebook',
                'line_id',
                'instagram',
            ]);
        });
    }
};
