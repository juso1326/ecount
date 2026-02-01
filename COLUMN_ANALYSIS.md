# 收支表欄位分析與優化建議

## Receivables (應收帳款) 欄位分析

### 基礎欄位 (必須保留)
- `id` - 主鍵
- `receipt_no` - 收據編號 ✅
- `project_id` - 所屬專案 ✅
- `company_id` - 客戶公司 ✅
- `amount` - 總金額 ✅
- `status` - 狀態 ✅
- `timestamps` - 建立/更新時間 ✅

### 日期欄位 (可優化)
- `receipt_date` - 收據日期 ✅ **保留**
- `issue_date` - 開立日 ⚠️ **可考慮刪除** (與 receipt_date 重複)
- `due_date` - 到期日 ✅ **保留**
- `paid_date` - 實際收款日期 ✅ **保留**

**建議：刪除 `issue_date`，統一使用 `receipt_date`**

### 金額欄位 (可優化)
- `amount` - 應收金額 (含稅總額) ✅
- `amount_before_tax` - 未稅額 ✅
- `tax_amount` - 營業稅額 ✅
- `withholding_tax` - 扣繳稅額 ✅
- `received_amount` - 已收金額 ✅ (原 paid_amount)
- `remaining_amount` - 未收金額 ❌ **可刪除** (可計算: amount - received_amount)
- `net_amount` - 實際入帳金額 ❌ **可刪除** (可計算: received_amount - withholding_tax)

**建議：刪除 `remaining_amount` 和 `net_amount`，改用計算屬性**

### 稅務欄位
- `has_tax` - 是否含營業稅 ⚠️ **可考慮刪除** (若 tax_amount > 0 即含稅)
- `tax_rate` - 營業稅率(%) ⚠️ **可考慮刪除** (台灣固定5%，可用常數)

**建議：刪除 `has_tax` 和 `tax_rate`，簡化結構**

### 其他欄位
- `content` - 內容說明 ✅ **保留**
- `quote_no` - 報價單號 ✅ **保留**
- `invoice_no` - 發票號碼 ✅ **保留**
- `payment_method` - 付款方式 ✅ **保留**
- `responsible_user_id` - 負責人 ✅ **保留**
- `note` - 備註 ✅ **保留**

---

## Payables (應付帳款) 欄位分析

### 基礎欄位 (必須保留)
- `id` - 主鍵
- `payment_no` - 付款單號 ✅
- `project_id` - 所屬專案 ✅
- `company_id` - 供應商公司 ✅
- `amount` - 總金額 ✅
- `status` - 狀態 ✅
- `timestamps` - 建立/更新時間 ✅

### 日期欄位
- `payment_date` - 付款日期 ✅ **保留**
- `invoice_date` - 發票日期 ✅ **保留**
- `due_date` - 到期日 ✅ **保留**
- `paid_date` - 實際付款日期 ✅ **保留**

### 金額欄位 (可優化)
- `amount` - 應付金額 ✅
- `deduction` - 扣抵金額 ✅
- `paid_amount` - 已付金額 ✅
- `remaining_amount` - 未付金額 ❌ **可刪除** (可計算: amount - paid_amount)
- `net_amount` - 實際支付金額 ❌ **可刪除** (可計算: paid_amount - deduction)

**建議：刪除 `remaining_amount` 和 `net_amount`**

### 其他欄位
- `type` - 類型 ✅ **保留** (外製/採購等)
- `vendor` - 對象名稱 ⚠️ **可考慮刪除** (已有 company_id)
- `content` - 內容說明 ✅ **保留**
- `invoice_no` - 發票號碼 ✅ **保留**
- `payment_method` - 付款方式 ✅ **保留**
- `responsible_user_id` - 負責人 ✅ **保留**
- `note` - 備註 ✅ **保留**

---

## 總結建議

### 強烈建議刪除 (可用計算屬性替代)
1. **Receivables:**
   - `remaining_amount` - 改用 `getRemainingAmountAttribute()`
   - `net_amount` - 改用 `getNetAmountAttribute()`

2. **Payables:**
   - `remaining_amount` - 改用 `getRemainingAmountAttribute()`
   - `net_amount` - 改用 `getNetAmountAttribute()`

### 考慮刪除 (可簡化結構)
1. **Receivables:**
   - `issue_date` - 與 receipt_date 重複
   - `has_tax` - 可用 tax_amount > 0 判斷
   - `tax_rate` - 台灣固定5%，用常數即可

2. **Payables:**
   - `vendor` - 已有 company_id 關聯

### 優點
- ✅ 減少資料冗餘
- ✅ 避免資料不一致
- ✅ 簡化維護邏輯
- ✅ 降低 migration 複雜度
- ✅ 計算欄位永遠正確

### 實作方式
使用 Laravel Eloquent Accessor:

```php
// Receivable Model
public function getRemainingAmountAttribute(): float
{
    return $this->amount - $this->received_amount;
}

public function getNetAmountAttribute(): float
{
    return $this->received_amount - $this->withholding_tax;
}

// Payable Model
public function getRemainingAmountAttribute(): float
{
    return $this->amount - $this->paid_amount;
}

public function getNetAmountAttribute(): float
{
    return $this->paid_amount - $this->deduction;
}
```
