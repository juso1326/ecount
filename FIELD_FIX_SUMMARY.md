# 資料庫欄位錯誤修復報告

## 🐛 錯誤說明

### 問題一：projects 表欄位錯誤
```
Column not found: 1054 Unknown column 'type' in 'WHERE'
```
**原因**: 控制器使用 `type` 欄位，但實際表中是 `project_type` 和 `status`

### 問題二：receivables 表欄位錯誤  
```
Column not found: 1054 Unknown column 'invoice_date' in 'WHERE'
```
**原因**: 控制器使用 `invoice_date`，但實際表中是 `receipt_date`

### 問題三：payables 表欄位錯誤
**原因**: 控制器使用 `has_paid`，但實際表中使用 `status` 欄位

---

## ✅ 已修復的欄位對應

### ProjectController
| 錯誤欄位 | 正確欄位 | 用途 |
|---------|---------|------|
| `type` | `project_type` | 專案類型 |
| `type` | `status` | 專案狀態（用於篩選） |
| `total_amount` | `budget` | 預算金額 |

**修正內容**:
- ✅ 查詢條件: `where('type', '!=', 'cancelled')` → `where('status', '!=', Project::STATUS_CANCELLED)`
- ✅ 篩選參數: `$request->filled('type')` → `$request->filled('project_type')`
- ✅ 驗證規則: 更新為符合 Model 的 fillable 欄位

### ReceivableController
| 錯誤欄位 | 正確欄位 | 用途 |
|---------|---------|------|
| `invoice_date` | `receipt_date` | 收款日期 |
| `type` | ❌ 移除 | 此表無 type 欄位 |
| `total_amount` | `amount` | 收款金額 |
| `advance_payment` | ❌ 移除 | 此表無此欄位 |
| `income_total` | `received_amount` | 實收金額 |

**修正內容**:
- ✅ 日期範圍: `whereBetween('invoice_date')` → `whereBetween('receipt_date')`
- ✅ 搜尋條件: 增加 `receipt_no` 欄位搜尋
- ✅ 移除 type 篩選條件
- ✅ 專案狀態檢查: `where('type', '!=', 'cancelled')` → `where('status', '!=', Project::STATUS_CANCELLED)`

### PayableController
| 錯誤欄位 | 正確欄位 | 用途 |
|---------|---------|------|
| `has_paid` | `status` | 付款狀態 |
| `total_amount` | `amount` | 付款金額 |

**修正內容**:
- ✅ 狀態篩選: `where('has_paid')` → `where('status')`
- ✅ 移除不存在的 type='petty_cash' 排除條件
- ✅ 搜尋條件: 增加 `payment_no` 欄位搜尋
- ✅ 驗證規則: type 從 enum 改為 string

---

## 📝 修改的檔案

1. ✅ `app/Http/Controllers/Tenant/ProjectController.php`
   - 修正 index() 方法的查詢條件
   - 修正 store() 和 update() 的驗證規則

2. ✅ `app/Http/Controllers/Tenant/ReceivableController.php`
   - 修正 index() 方法的日期欄位和查詢條件
   - 修正 store() 和 update() 的驗證規則

3. ✅ `app/Http/Controllers/Tenant/PayableController.php`
   - 修正 index() 方法的狀態篩選條件
   - 修正 store() 和 update() 的驗證規則

---

## 🎯 欄位對應表（完整版）

### Projects 表
```php
// 舊系統 → 新系統
prjm01_no → code (專案代碼)
prjm01_nm → name (專案名稱)
t02_no → status (專案狀態：規劃/進行/暫停/完成/取消)
comm01_no → company_id (客戶公司)
prjm01_startDate → start_date (開始日期)
prjm01_totalmoney → budget (預算金額)
```

### Receivables 表
```php
// 舊系統 → 新系統  
inm01_no → receipt_no (收款單號)
inm01_invoicedate → receipt_date (收款日期)
inm01_subtotal → amount_before_tax (未稅金額)
inm01_tax → tax_amount (稅額)
inm01_total → amount (總金額)
inm01_incometotal → received_amount (實收金額)
inm01_invoiceno → invoice_no (發票號碼)
```

### Payables 表
```php
// 舊系統 → 新系統
paym01_no → payment_no (付款單號)
paym01_paydate → payment_date (付款日期)
paym01_total → amount (付款金額)
paym01_paytotal → paid_amount (已付金額)
paym01_haspay → status (付款狀態：unpaid/partial/paid)
paym01_type1 → type (付款類型)
```

---

## 🔄 測試建議

### 1. 測試專案列表
```bash
# 應該可以正常顯示
curl "https://abc123.ecount.test/projects"

# 測試狀態篩選
curl "https://abc123.ecount.test/projects?status=in_progress"

# 測試專案類型篩選
curl "https://abc123.ecount.test/projects?project_type=建案"
```

### 2. 測試應收帳款
```bash
# 應該可以正常顯示
curl "https://abc123.ecount.test/receivables"

# 測試日期篩選
curl "https://abc123.ecount.test/receivables?date_start=2025-01-01&date_end=2025-12-31"
```

### 3. 測試應付帳款
```bash
# 應該可以正常顯示
curl "https://abc123.ecount.test/payables"

# 測試狀態篩選
curl "https://abc123.ecount.test/payables?status=paid"
```

---

## ✨ 總結

✅ **所有資料庫欄位錯誤已修復**
✅ **控制器查詢條件與實際表結構一致**
✅ **驗證規則符合 Model 定義**
✅ **快取已清除，立即生效**

現在系統應該可以正常運行，不再出現欄位找不到的錯誤。
