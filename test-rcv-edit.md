# 應收帳款編輯頁測試報告

## 測試環境
- URL: https://abc123.ecount.test/receivables/11/edit
- 測試資料: ID 11
- 測試時間: 2026-02-20

## 修復前資料狀態

```
應收帳款 ID 11:
- receipt_no: RCV-202602-003
- amount_before_tax: 0.00 ❌
- tax_amount: 0.00
- amount: 210,894.00
```

**問題：**
- 金額欄位顯示：0.00 ❌
- 總計欄位顯示：210,894.00 ✅

## 修復步驟

### 1. JavaScript 修復 (Commit: 6f9755d)
```javascript
// 編輯模式不執行 calculateTax()
@if(!isset($receivable))
calculateTax();
@endif
```

### 2. 資料修復
```php
// 修復 37 筆應收帳款資料不一致問題
Receivable::where('amount', '>', 0)
    ->where('amount_before_tax', 0)
    ->update(['amount_before_tax' => DB::raw('amount')]);
```

## 修復後資料狀態

```
應收帳款 ID 11:
- amount_before_tax: 210,894.00 ✅
- tax_amount: 0.00
- amount: 210,894.00 ✅
```

## 測試結果

✅ **金額欄位：210,894.00** (正確顯示)
✅ **總計欄位：210,894.00** (正確顯示)

## 影響範圍

**應收帳款：** 37 筆資料已修復
**應付帳款：** 不適用（無 amount_before_tax 欄位）

## 測試結論

✅ 問題已完全解決
✅ 編輯頁面金額正確顯示
✅ 不影響新增功能
