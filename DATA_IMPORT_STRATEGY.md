# 舊資料匯入策略

## 問題：刪除的欄位在匯入時如何處理？

### ✅ 答案：只需匯入基本欄位，計算欄位會自動產生

## 範例：Receivables (應收帳款) 匯入

### 舊系統欄位
```
pay_t02 表:
- payt02_total: 100000      (總金額)
- payt02_paid: 60000        (已付金額)
- payt02_remaining: 40000   (未付金額) ← 冗餘欄位
- payt02_net: 58000         (實際入帳) ← 冗餘欄位
- payt02_tax: 2000          (扣繳稅)
```

### 新系統只需匯入這些
```php
Receivable::create([
    'receipt_no' => 'R001',
    'project_id' => 1,
    'company_id' => 1,
    'amount' => 100000,           // 從 payt02_total
    'received_amount' => 60000,   // 從 payt02_paid
    'withholding_tax' => 2000,    // 從 payt02_tax
    // 不需要匯入 remaining_amount
    // 不需要匯入 net_amount
]);
```

### 自動計算的欄位
```php
$receivable = Receivable::find(1);

// 這些會自動計算，不佔資料庫空間
$receivable->remaining_amount;  // 40000 (100000 - 60000)
$receivable->net_amount;        // 58000 (60000 - 2000)
```

---

## 匯入程式範例

### 方法 1: Command 匯入
```php
// app/Console/Commands/ImportOldReceivables.php
class ImportOldReceivables extends Command
{
    public function handle()
    {
        // 連接舊資料庫
        $oldData = DB::connection('old_db')
            ->table('pay_t02')
            ->where('payt02_type', 'income')
            ->get();

        foreach ($oldData as $old) {
            Receivable::create([
                'receipt_no' => $old->payt02_no,
                'receipt_date' => $this->convertDate($old->payt02_date),
                'amount' => $old->payt02_total,
                'amount_before_tax' => $old->payt02_before_tax ?? 0,
                'tax_amount' => $old->payt02_tax_amount ?? 0,
                'withholding_tax' => $old->payt02_tax ?? 0,
                'received_amount' => $old->payt02_paid,
                'status' => $this->mapStatus($old),
                // 不匯入計算欄位
                // 'remaining_amount' => $old->payt02_remaining, ← 不需要
                // 'net_amount' => $old->payt02_net,             ← 不需要
            ]);
        }
    }
}
```

### 方法 2: 批次匯入
```php
// 大量資料使用 insert 提升效能
$records = [];
foreach ($oldData as $old) {
    $records[] = [
        'receipt_no' => $old->payt02_no,
        'amount' => $old->payt02_total,
        'received_amount' => $old->payt02_paid,
        'withholding_tax' => $old->payt02_tax,
        'created_at' => now(),
        'updated_at' => now(),
        // 不包含計算欄位
    ];
}

Receivable::insert($records);
```

---

## 已刪除欄位的對應關係

### Receivables
| 舊欄位 | 新系統處理方式 |
|--------|--------------|
| `remaining_amount` | ✅ 自動計算：`amount - received_amount` |
| `net_amount` | ✅ 自動計算：`received_amount - withholding_tax` |
| `issue_date` | ✅ 使用 `receipt_date` 替代 |
| `has_tax` | ✅ 自動判斷：`tax_amount > 0` |

### Payables
| 舊欄位 | 新系統處理方式 |
|--------|--------------|
| `remaining_amount` | ✅ 自動計算：`amount - paid_amount` |
| `net_amount` | ✅ 自動計算：`paid_amount - deduction` |
| `vendor` | ✅ 使用 `company` 關聯取得 `company->name` |

---

## 驗證資料正確性

匯入後可以驗證計算是否正確：

```php
// 驗證腳本
Receivable::chunk(100, function ($receivables) {
    foreach ($receivables as $r) {
        // 驗證未收金額
        $expected = $r->amount - $r->received_amount;
        $actual = $r->remaining_amount;
        
        if ($expected != $actual) {
            Log::error("Receivable {$r->id} 計算錯誤");
        }
    }
});
```

---

## 優點總結

✅ **匯入更簡單**：只需匯入必要欄位
✅ **資料永遠正確**：計算欄位無法出錯
✅ **節省空間**：不儲存冗餘資料
✅ **效能不受影響**：計算非常快速
✅ **維護容易**：只有一個真實來源

## 效能考量

Q: 每次存取都計算，會不會很慢？
A: 不會！原因：
1. 計算非常簡單（加減法）
2. PHP 執行速度極快（微秒級）
3. 如需快取可用 `$appends`

```php
// 如需在 JSON 中自動包含計算欄位
protected $appends = ['remaining_amount', 'net_amount'];
```
