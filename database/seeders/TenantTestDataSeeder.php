<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Department;
use App\Models\Project;
use App\Models\Receivable;
use App\Models\Payable;
use App\Models\Code;

class TenantTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. 建立代碼表資料
        $this->createCodes();
        
        // 2. 建立公司資料
        $companies = $this->createCompanies();
        
        // 3. 建立部門資料
        $departments = $this->createDepartments();
        
        // 4. 建立專案資料
        $projects = $this->createProjects($companies, $departments);
        
        // 5. 建立應收帳款資料
        $this->createReceivables($projects, $companies);
        
        // 6. 建立應付帳款資料
        $this->createPayables($projects);
        
        $this->command->info('測試資料建立完成！');
    }
    
    /**
     * 建立代碼表
     */
    private function createCodes()
    {
        $codes = [
            // 專案狀態
            ['type' => 'project_status', 'code' => 'planning', 'name' => '規劃中', 'sort_order' => 1],
            ['type' => 'project_status', 'code' => 'in_progress', 'name' => '進行中', 'sort_order' => 2],
            ['type' => 'project_status', 'code' => 'completed', 'name' => '已完成', 'sort_order' => 3],
            ['type' => 'project_status', 'code' => 'suspended', 'name' => '暫停', 'sort_order' => 4],
            ['type' => 'project_status', 'code' => 'cancelled', 'name' => '取消', 'sort_order' => 5],
            
            // 付款方式
            ['type' => 'payment_method', 'code' => 'cash', 'name' => '現金', 'sort_order' => 1],
            ['type' => 'payment_method', 'code' => 'transfer', 'name' => '匯款', 'sort_order' => 2],
            ['type' => 'payment_method', 'code' => 'check', 'name' => '支票', 'sort_order' => 3],
            ['type' => 'payment_method', 'code' => 'credit_card', 'name' => '信用卡', 'sort_order' => 4],
            
            // 應收應付狀態
            ['type' => 'payment_status', 'code' => 'unpaid', 'name' => '未付', 'sort_order' => 1],
            ['type' => 'payment_status', 'code' => 'partial', 'name' => '部分付款', 'sort_order' => 2],
            ['type' => 'payment_status', 'code' => 'paid', 'name' => '已付', 'sort_order' => 3],
            ['type' => 'payment_status', 'code' => 'overdue', 'name' => '逾期', 'sort_order' => 4],
            
            // 公司類型
            ['type' => 'company_type', 'code' => 'client', 'name' => '客戶', 'sort_order' => 1],
            ['type' => 'company_type', 'code' => 'vendor', 'name' => '廠商', 'sort_order' => 2],
            ['type' => 'company_type', 'code' => 'partner', 'name' => '合作夥伴', 'sort_order' => 3],
        ];
        
        foreach ($codes as $code) {
            Code::create($code);
        }
        
        $this->command->info('✓ 代碼表建立完成 (' . count($codes) . ' 筆)');
    }
    
    /**
     * 建立公司資料
     */
    private function createCompanies()
    {
        $companies = [
            [
                'code' => 'C001',
                'name' => '台積電股份有限公司',
                'short_name' => '台積電',
                'tax_id' => '12345678',
                'contact_person' => '張經理',
                'phone' => '02-1234-5678',
                'email' => 'contact@tsmc.com',
                'address' => '新竹市科學園區',
                'type' => 'client',
                'status' => 'active',
            ],
            [
                'code' => 'C002',
                'name' => '聯發科技股份有限公司',
                'short_name' => '聯發科',
                'tax_id' => '23456789',
                'contact_person' => '李副理',
                'phone' => '03-5670-8888',
                'email' => 'info@mediatek.com',
                'address' => '新竹市科學園區',
                'type' => 'client',
                'status' => 'active',
            ],
            [
                'code' => 'C003',
                'name' => '華碩電腦股份有限公司',
                'short_name' => '華碩',
                'tax_id' => '34567890',
                'contact_person' => '王主任',
                'phone' => '02-2894-3447',
                'email' => 'service@asus.com',
                'address' => '台北市北投區',
                'type' => 'client',
                'status' => 'active',
            ],
            [
                'code' => 'V001',
                'name' => '大聯大控股股份有限公司',
                'short_name' => '大聯大',
                'tax_id' => '45678901',
                'contact_person' => '陳經理',
                'phone' => '02-2788-5200',
                'email' => 'contact@wpgholdings.com',
                'address' => '台北市內湖區',
                'type' => 'vendor',
                'status' => 'active',
            ],
            [
                'code' => 'V002',
                'name' => '欣技資訊股份有限公司',
                'short_name' => '欣技',
                'tax_id' => '56789012',
                'contact_person' => '林協理',
                'phone' => '02-8797-8123',
                'email' => 'info@syntech.com.tw',
                'address' => '台北市內湖區',
                'type' => 'vendor',
                'status' => 'active',
            ],
        ];
        
        $created = [];
        foreach ($companies as $company) {
            $created[] = Company::create($company);
        }
        
        $this->command->info('✓ 公司資料建立完成 (' . count($created) . ' 筆)');
        return $created;
    }
    
    /**
     * 建立部門資料
     */
    private function createDepartments()
    {
        $departments = [
            ['code' => 'D01', 'name' => '業務部', 'description' => '負責業務開發與客戶關係維護', 'status' => 'active'],
            ['code' => 'D02', 'name' => '技術部', 'description' => '負責技術研發與專案執行', 'status' => 'active'],
            ['code' => 'D03', 'name' => '財務部', 'description' => '負責財務管理與會計作業', 'status' => 'active'],
            ['code' => 'D04', 'name' => '行政部', 'description' => '負責行政支援與總務管理', 'status' => 'active'],
            ['code' => 'D05', 'name' => '專案管理部', 'description' => '負責專案規劃與進度管控', 'status' => 'active'],
        ];
        
        $created = [];
        foreach ($departments as $dept) {
            $created[] = Department::create($dept);
        }
        
        $this->command->info('✓ 部門資料建立完成 (' . count($created) . ' 筆)');
        return $created;
    }
    
    /**
     * 建立專案資料
     */
    private function createProjects($companies, $departments)
    {
        $projects = [
            [
                'code' => 'PJ2024001',
                'name' => '台積電 ERP 系統導入專案',
                'company_id' => $companies[0]->id,
                'department_id' => $departments[1]->id,
                'budget' => 5000000,
                'start_date' => '2024-01-15',
                'end_date' => '2024-12-31',
                'status' => 'in_progress',
                'description' => 'ERP 系統導入與客製化開發',
            ],
            [
                'code' => 'PJ2024002',
                'name' => '聯發科官網改版專案',
                'company_id' => $companies[1]->id,
                'department_id' => $departments[1]->id,
                'budget' => 800000,
                'start_date' => '2024-03-01',
                'end_date' => '2024-06-30',
                'status' => 'completed',
                'description' => '企業官網設計與開發',
            ],
            [
                'code' => 'PJ2024003',
                'name' => '華碩庫存管理系統',
                'company_id' => $companies[2]->id,
                'department_id' => $departments[1]->id,
                'budget' => 1200000,
                'start_date' => '2024-05-10',
                'end_date' => '2024-10-31',
                'status' => 'in_progress',
                'description' => '倉儲管理系統開發',
            ],
            [
                'code' => 'PJ2025001',
                'name' => '聯發科行動應用開發',
                'company_id' => $companies[1]->id,
                'department_id' => $departments[1]->id,
                'budget' => 1500000,
                'start_date' => '2025-01-01',
                'end_date' => '2025-08-31',
                'status' => 'in_progress',
                'description' => 'iOS/Android 雙平台應用開發',
            ],
            [
                'code' => 'PJ2025002',
                'name' => '台積電數據分析平台',
                'company_id' => $companies[0]->id,
                'department_id' => $departments[4]->id,
                'budget' => 3000000,
                'start_date' => '2025-02-01',
                'end_date' => '2025-12-31',
                'status' => 'planning',
                'description' => '大數據分析與視覺化平台建置',
            ],
        ];
        
        $created = [];
        foreach ($projects as $project) {
            $created[] = Project::create($project);
        }
        
        $this->command->info('✓ 專案資料建立完成 (' . count($created) . ' 筆)');
        return $created;
    }
    
    /**
     * 建立應收帳款資料
     */
    private function createReceivables($projects, $companies)
    {
        $receivables = [
            [
                'project_id' => $projects[0]->id,
                'company_id' => $companies[0]->id,
                'receipt_no' => 'R202401001',
                'invoice_no' => 'AA12345678',
                'receipt_date' => '2024-02-15',
                'due_date' => '2024-03-15',
                'amount' => 1000000,
                'amount_before_tax' => 952380.95,
                'tax_amount' => 47619.05,
                'withholding_tax' => 0,
                'received_amount' => 1000000,
                'status' => 'paid',
                'content' => '第一期款項',
                'payment_method' => 'transfer',
                'note' => '已收款完成',
            ],
            [
                'project_id' => $projects[0]->id,
                'company_id' => $companies[0]->id,
                'receipt_no' => 'R202404001',
                'invoice_no' => 'AA23456789',
                'receipt_date' => '2024-05-20',
                'due_date' => '2024-06-20',
                'amount' => 1500000,
                'amount_before_tax' => 1428571.43,
                'tax_amount' => 71428.57,
                'withholding_tax' => 20000,
                'received_amount' => 1480000,
                'status' => 'paid',
                'content' => '第二期款項',
                'payment_method' => 'transfer',
                'note' => '已扣繳 20,000',
            ],
            [
                'project_id' => $projects[1]->id,
                'company_id' => $companies[1]->id,
                'receipt_no' => 'R202405001',
                'invoice_no' => 'AB12345678',
                'receipt_date' => '2024-06-10',
                'due_date' => '2024-07-10',
                'amount' => 800000,
                'amount_before_tax' => 761904.76,
                'tax_amount' => 38095.24,
                'withholding_tax' => 10000,
                'received_amount' => 790000,
                'status' => 'paid',
                'content' => '官網改版驗收款',
                'payment_method' => 'transfer',
                'note' => '專案已結案',
            ],
            [
                'project_id' => $projects[2]->id,
                'company_id' => $companies[2]->id,
                'receipt_no' => 'R202407001',
                'invoice_no' => 'AC12345678',
                'receipt_date' => '2024-08-15',
                'due_date' => '2024-09-15',
                'amount' => 600000,
                'amount_before_tax' => 571428.57,
                'tax_amount' => 28571.43,
                'withholding_tax' => 0,
                'received_amount' => 300000,
                'status' => 'partial',
                'content' => '第一期開發款',
                'payment_method' => 'transfer',
                'note' => '已收 50%',
            ],
            [
                'project_id' => $projects[3]->id,
                'company_id' => $companies[1]->id,
                'receipt_no' => 'R202501001',
                'invoice_no' => 'AB23456789',
                'receipt_date' => '2025-03-01',
                'due_date' => '2025-04-01',
                'amount' => 750000,
                'amount_before_tax' => 714285.71,
                'tax_amount' => 35714.29,
                'withholding_tax' => 0,
                'received_amount' => 0,
                'status' => 'unpaid',
                'content' => 'App 開發第一期',
                'payment_method' => 'transfer',
                'note' => '待收款',
            ],
            [
                'project_id' => $projects[2]->id,
                'company_id' => $companies[2]->id,
                'receipt_no' => 'R202412001',
                'invoice_no' => 'AC23456789',
                'receipt_date' => '2024-11-20',
                'due_date' => '2024-12-20',
                'amount' => 600000,
                'amount_before_tax' => 571428.57,
                'tax_amount' => 28571.43,
                'withholding_tax' => 0,
                'received_amount' => 0,
                'status' => 'overdue',
                'content' => '第二期開發款',
                'payment_method' => 'transfer',
                'note' => '逾期未付',
            ],
        ];
        
        foreach ($receivables as $receivable) {
            Receivable::create($receivable);
        }
        
        $this->command->info('✓ 應收帳款建立完成 (' . count($receivables) . ' 筆)');
    }
    
    /**
     * 建立應付帳款資料
     */
    private function createPayables($projects)
    {
        $payables = [
            [
                'project_id' => $projects[0]->id,
                'vendor_name' => '大聯大控股',
                'payment_date' => '2024-02-01',
                'due_date' => '2024-02-28',
                'amount' => 200000,
                'paid_amount' => 200000,
                'status' => 'paid',
                'content' => '硬體設備採購',
                'payment_method' => 'transfer',
                'note' => '已付款',
            ],
            [
                'project_id' => $projects[0]->id,
                'vendor_name' => '欣技資訊',
                'payment_date' => '2024-03-15',
                'due_date' => '2024-04-15',
                'amount' => 150000,
                'paid_amount' => 150000,
                'status' => 'paid',
                'content' => '軟體授權費用',
                'payment_method' => 'transfer',
                'note' => '已付款',
            ],
            [
                'project_id' => $projects[1]->id,
                'vendor_name' => '設計公司A',
                'payment_date' => '2024-04-01',
                'due_date' => '2024-04-30',
                'amount' => 120000,
                'paid_amount' => 120000,
                'status' => 'paid',
                'content' => 'UI/UX 設計費',
                'payment_method' => 'transfer',
                'note' => '已付款',
            ],
            [
                'project_id' => $projects[2]->id,
                'vendor_name' => '大聯大控股',
                'payment_date' => '2024-06-10',
                'due_date' => '2024-07-10',
                'amount' => 180000,
                'paid_amount' => 90000,
                'status' => 'partial',
                'content' => '伺服器採購',
                'payment_method' => 'transfer',
                'note' => '已付 50%',
            ],
            [
                'project_id' => $projects[3]->id,
                'vendor_name' => '欣技資訊',
                'payment_date' => '2025-02-01',
                'due_date' => '2025-03-01',
                'amount' => 100000,
                'paid_amount' => 0,
                'status' => 'unpaid',
                'content' => 'App 開發工具授權',
                'payment_method' => 'transfer',
                'note' => '待付款',
            ],
            [
                'project_id' => $projects[2]->id,
                'vendor_name' => '網路服務商B',
                'payment_date' => '2024-12-01',
                'due_date' => '2024-12-31',
                'amount' => 80000,
                'paid_amount' => 0,
                'status' => 'overdue',
                'content' => '雲端服務費用',
                'payment_method' => 'transfer',
                'note' => '逾期未付',
            ],
        ];
        
        foreach ($payables as $payable) {
            Payable::create($payable);
        }
        
        $this->command->info('✓ 應付帳款建立完成 (' . count($payables) . ' 筆)');
    }
}
