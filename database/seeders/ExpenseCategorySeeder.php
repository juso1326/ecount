<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['code' => 'PERSON', 'name' => '人事費用', 'sort_order' => 1, 'children' => [
                ['code' => 'PERSON-01', 'name' => '薪資',     'sort_order' => 1],
                ['code' => 'PERSON-02', 'name' => '勞健保',   'sort_order' => 2],
                ['code' => 'PERSON-03', 'name' => '加班費',   'sort_order' => 3],
                ['code' => 'PERSON-04', 'name' => '退休金',   'sort_order' => 4],
                ['code' => 'PERSON-05', 'name' => '員工福利', 'sort_order' => 5],
            ]],
            ['code' => 'OUTSOURCE', 'name' => '外包費用', 'sort_order' => 2, 'children' => [
                ['code' => 'OUTSOURCE-01', 'name' => '外包開發',   'sort_order' => 1],
                ['code' => 'OUTSOURCE-02', 'name' => '設計外包',   'sort_order' => 2],
                ['code' => 'OUTSOURCE-03', 'name' => '顧問費',     'sort_order' => 3],
                ['code' => 'OUTSOURCE-04', 'name' => '翻譯費',     'sort_order' => 4],
            ]],
            ['code' => 'OFFICE', 'name' => '辦公費用', 'sort_order' => 3, 'children' => [
                ['code' => 'OFFICE-01', 'name' => '租金',       'sort_order' => 1],
                ['code' => 'OFFICE-02', 'name' => '水電費',     'sort_order' => 2],
                ['code' => 'OFFICE-03', 'name' => '電話網路費', 'sort_order' => 3],
                ['code' => 'OFFICE-04', 'name' => '辦公用品',   'sort_order' => 4],
                ['code' => 'OFFICE-05', 'name' => '清潔費',     'sort_order' => 5],
            ]],
            ['code' => 'EQUIP', 'name' => '設備費用', 'sort_order' => 4, 'children' => [
                ['code' => 'EQUIP-01', 'name' => '硬體設備',   'sort_order' => 1],
                ['code' => 'EQUIP-02', 'name' => '軟體授權',   'sort_order' => 2],
                ['code' => 'EQUIP-03', 'name' => '雲端服務',   'sort_order' => 3],
                ['code' => 'EQUIP-04', 'name' => '設備維修',   'sort_order' => 4],
            ]],
            ['code' => 'TRAVEL', 'name' => '差旅費用', 'sort_order' => 5, 'children' => [
                ['code' => 'TRAVEL-01', 'name' => '交通費', 'sort_order' => 1],
                ['code' => 'TRAVEL-02', 'name' => '住宿費', 'sort_order' => 2],
                ['code' => 'TRAVEL-03', 'name' => '餐費',   'sort_order' => 3],
                ['code' => 'TRAVEL-04', 'name' => '出差費', 'sort_order' => 4],
            ]],
            ['code' => 'MKTG', 'name' => '行銷費用', 'sort_order' => 6, 'children' => [
                ['code' => 'MKTG-01', 'name' => '廣告費',   'sort_order' => 1],
                ['code' => 'MKTG-02', 'name' => '展覽費',   'sort_order' => 2],
                ['code' => 'MKTG-03', 'name' => '印刷品',   'sort_order' => 3],
                ['code' => 'MKTG-04', 'name' => '公關費',   'sort_order' => 4],
            ]],
            ['code' => 'TAX', 'name' => '稅費', 'sort_order' => 7, 'children' => [
                ['code' => 'TAX-01', 'name' => '營業稅', 'sort_order' => 1],
                ['code' => 'TAX-02', 'name' => '所得稅', 'sort_order' => 2],
                ['code' => 'TAX-03', 'name' => '印花稅', 'sort_order' => 3],
            ]],
            ['code' => 'OTHER', 'name' => '其他費用', 'sort_order' => 8, 'children' => [
                ['code' => 'OTHER-01', 'name' => '銀行手續費', 'sort_order' => 1],
                ['code' => 'OTHER-02', 'name' => '雜支',       'sort_order' => 2],
                ['code' => 'OTHER-03', 'name' => '捐款',       'sort_order' => 3],
            ]],
        ];

        foreach ($categories as $parentData) {
            $children = $parentData['children'] ?? [];
            unset($parentData['children']);
            $parentData['is_active'] = true;

            $parent = ExpenseCategory::create($parentData);

            foreach ($children as $childData) {
                $childData['parent_id'] = $parent->id;
                $childData['is_active'] = true;
                ExpenseCategory::create($childData);
            }
        }

        $this->command->info('✓ 支出分類建立完成 (' . ExpenseCategory::count() . ' 筆)');
    }
}
