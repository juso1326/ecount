<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Project;
use App\Models\Receivable;
use App\Models\Payable;

class TenantTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $companies  = $this->createCompanies();
        $projects   = $this->createProjects($companies);
        $this->createReceivables($projects, $companies);
        $this->createPayables($projects, $companies);

        $this->command->info('測試資料建立完成！');
    }

    private function createCompanies()
    {
        $rows = [
            ['code'=>'C001','name'=>'台積電股份有限公司','short_name'=>'台積電','type'=>'company','is_client'=>true,'tax_id'=>'12345678','contact_person'=>'張經理','phone'=>'02-1234-5678','email'=>'contact@tsmc.com','address'=>'新竹市科學園區','is_active'=>true],
            ['code'=>'C002','name'=>'聯發科技股份有限公司','short_name'=>'聯發科','type'=>'company','is_client'=>true,'tax_id'=>'23456789','contact_person'=>'李副理','phone'=>'03-5670-8888','email'=>'info@mediatek.com','address'=>'新竹市科學園區','is_active'=>true],
            ['code'=>'C003','name'=>'華碩電腦股份有限公司','short_name'=>'華碩','type'=>'company','is_client'=>true,'tax_id'=>'34567890','contact_person'=>'王主任','phone'=>'02-2894-3447','email'=>'service@asus.com','address'=>'台北市北投區','is_active'=>true],
            ['code'=>'V001','name'=>'大聯大控股股份有限公司','short_name'=>'大聯大','type'=>'company','is_client'=>false,'is_outsource'=>true,'tax_id'=>'45678901','contact_person'=>'陳經理','phone'=>'02-2788-5200','email'=>'contact@wpgholdings.com','address'=>'台北市內湖區','is_active'=>true],
            ['code'=>'V002','name'=>'欣技資訊股份有限公司','short_name'=>'欣技','type'=>'company','is_client'=>false,'is_outsource'=>true,'tax_id'=>'56789012','contact_person'=>'林協理','phone'=>'02-8797-8123','email'=>'info@syntech.com.tw','address'=>'台北市內湖區','is_active'=>true],
        ];

        $created = [];
        foreach ($rows as $row) {
            $created[] = Company::create($row);
        }
        $this->command->info('✓ 公司資料建立完成 (' . count($created) . ' 筆)');
        return $created;
    }

    private function createProjects($companies)
    {
        $rows = [
            ['code'=>'PJ2024001','name'=>'台積電 ERP 系統導入專案','company_id'=>$companies[0]->id,'budget'=>5000000,'start_date'=>'2024-01-15','end_date'=>'2024-12-31','status'=>'in_progress','description'=>'ERP 系統導入與客製化開發'],
            ['code'=>'PJ2024002','name'=>'聯發科官網改版專案','company_id'=>$companies[1]->id,'budget'=>800000,'start_date'=>'2024-03-01','end_date'=>'2024-06-30','status'=>'completed','description'=>'企業官網設計與開發'],
            ['code'=>'PJ2024003','name'=>'華碩庫存管理系統','company_id'=>$companies[2]->id,'budget'=>1200000,'start_date'=>'2024-05-10','end_date'=>'2024-10-31','status'=>'in_progress','description'=>'倉儲管理系統開發'],
            ['code'=>'PJ2025001','name'=>'聯發科行動應用開發','company_id'=>$companies[1]->id,'budget'=>1500000,'start_date'=>'2025-01-01','end_date'=>'2025-08-31','status'=>'in_progress','description'=>'iOS/Android 雙平台應用開發'],
            ['code'=>'PJ2025002','name'=>'台積電數據分析平台','company_id'=>$companies[0]->id,'budget'=>3000000,'start_date'=>'2025-02-01','end_date'=>'2025-12-31','status'=>'planning','description'=>'大數據分析與視覺化平台建置'],
        ];

        $created = [];
        foreach ($rows as $row) {
            $created[] = Project::create($row);
        }
        $this->command->info('✓ 專案資料建立完成 (' . count($created) . ' 筆)');
        return $created;
    }

    private function createReceivables($projects, $companies)
    {
        $rows = [
            ['project_id'=>$projects[0]->id,'company_id'=>$companies[0]->id,'receipt_no'=>'RCP-2024-001','invoice_no'=>'AA12345678','receipt_date'=>'2024-02-15','due_date'=>'2024-03-15','amount'=>1000000,'amount_before_tax'=>952381,'tax_rate'=>5,'tax_amount'=>47619,'received_amount'=>1000000,'status'=>'paid','content'=>'第一期款項','payment_method'=>'transfer'],
            ['project_id'=>$projects[0]->id,'company_id'=>$companies[0]->id,'receipt_no'=>'RCP-2024-002','invoice_no'=>'AA23456789','receipt_date'=>'2024-05-20','due_date'=>'2024-06-20','amount'=>1500000,'amount_before_tax'=>1428571,'tax_rate'=>5,'tax_amount'=>71429,'received_amount'=>1480000,'withholding_tax'=>20000,'status'=>'paid','content'=>'第二期款項','payment_method'=>'transfer'],
            ['project_id'=>$projects[1]->id,'company_id'=>$companies[1]->id,'receipt_no'=>'RCP-2024-003','invoice_no'=>'AB12345678','receipt_date'=>'2024-06-10','due_date'=>'2024-07-10','amount'=>800000,'amount_before_tax'=>761905,'tax_rate'=>5,'tax_amount'=>38095,'received_amount'=>790000,'withholding_tax'=>10000,'status'=>'paid','content'=>'官網改版驗收款','payment_method'=>'transfer'],
            ['project_id'=>$projects[2]->id,'company_id'=>$companies[2]->id,'receipt_no'=>'RCP-2024-004','invoice_no'=>'AC12345678','receipt_date'=>'2024-08-15','due_date'=>'2024-09-15','amount'=>600000,'amount_before_tax'=>571429,'tax_rate'=>5,'tax_amount'=>28571,'received_amount'=>300000,'status'=>'partial','content'=>'第一期開發款','payment_method'=>'transfer'],
            ['project_id'=>$projects[3]->id,'company_id'=>$companies[1]->id,'receipt_no'=>'RCP-2025-001','invoice_no'=>'AB23456789','receipt_date'=>'2025-03-01','due_date'=>'2025-04-01','amount'=>750000,'amount_before_tax'=>714286,'tax_rate'=>5,'tax_amount'=>35714,'received_amount'=>0,'status'=>'unpaid','content'=>'App 開發第一期','payment_method'=>'transfer'],
            ['project_id'=>$projects[2]->id,'company_id'=>$companies[2]->id,'receipt_no'=>'RCP-2024-005','invoice_no'=>'AC23456789','receipt_date'=>'2024-11-20','due_date'=>'2024-12-20','amount'=>600000,'amount_before_tax'=>571429,'tax_rate'=>5,'tax_amount'=>28571,'received_amount'=>0,'status'=>'overdue','content'=>'第二期開發款','payment_method'=>'transfer'],
        ];

        foreach ($rows as $row) {
            Receivable::create($row);
        }
        $this->command->info('✓ 應收帳款建立完成 (' . count($rows) . ' 筆)');
    }

    private function createPayables($projects, $companies)
    {
        $rows = [
            ['project_id'=>$projects[0]->id,'company_id'=>$companies[3]->id,'payment_no'=>'PAY-2024-001','content'=>'硬體設備採購','payment_date'=>'2024-02-01','due_date'=>'2024-02-28','amount'=>200000,'paid_amount'=>200000,'status'=>'paid','payment_method'=>'transfer'],
            ['project_id'=>$projects[0]->id,'company_id'=>$companies[4]->id,'payment_no'=>'PAY-2024-002','content'=>'軟體授權費用','payment_date'=>'2024-03-15','due_date'=>'2024-04-15','amount'=>150000,'paid_amount'=>150000,'status'=>'paid','payment_method'=>'transfer'],
            ['project_id'=>$projects[1]->id,'company_id'=>$companies[3]->id,'payment_no'=>'PAY-2024-003','content'=>'UI/UX 設計費','payment_date'=>'2024-04-01','due_date'=>'2024-04-30','amount'=>120000,'paid_amount'=>120000,'status'=>'paid','payment_method'=>'transfer'],
            ['project_id'=>$projects[2]->id,'company_id'=>$companies[3]->id,'payment_no'=>'PAY-2024-004','content'=>'伺服器採購','payment_date'=>'2024-06-10','due_date'=>'2024-07-10','amount'=>180000,'paid_amount'=>90000,'status'=>'partial','payment_method'=>'transfer'],
            ['project_id'=>$projects[3]->id,'company_id'=>$companies[4]->id,'payment_no'=>'PAY-2025-001','content'=>'App 開發工具授權','payment_date'=>'2025-02-01','due_date'=>'2025-03-01','amount'=>100000,'paid_amount'=>0,'status'=>'unpaid','payment_method'=>'transfer'],
            ['project_id'=>$projects[2]->id,'company_id'=>$companies[4]->id,'payment_no'=>'PAY-2024-005','content'=>'雲端服務費用','payment_date'=>'2024-12-01','due_date'=>'2024-12-31','amount'=>80000,'paid_amount'=>0,'status'=>'overdue','payment_method'=>'transfer'],
        ];

        foreach ($rows as $row) {
            Payable::create($row);
        }
        $this->command->info('✓ 應付帳款建立完成 (' . count($rows) . ' 筆)');
    }
}
