# 台幣無小數點格式化方案

## 🎯 目標
台幣（TWD）顯示時不需要小數點，因為最小單位是 1 元

## ✅ 實作方案

### 1. 建立 CurrencyHelper 類別
**位置：** `app/Helpers/CurrencyHelper.php`

**功能：**
- 自動根據貨幣決定小數位數
- TWD/JPY/KRW 等 → 0 位小數
- USD/EUR/GBP 等 → 2 位小數

**方法：**
```php
CurrencyHelper::format($amount, $currency, $showSymbol)
CurrencyHelper::getDecimalPlaces($currency)
CurrencyHelper::getCurrencySymbol($currency)
CurrencyHelper::jsFormatter($currency)
```

### 2. 全域 Helper 函數
**位置：** `app/Helpers/helpers.php`

**使用：**
```php
// PHP 端
format_currency(210894)           // NT$ 210,894
format_currency(1234.56, 'USD')   // US$ 1,234.56
currency_decimals('TWD')          // 0
```

### 3. Blade Directives
**位置：** `app/Providers/AppServiceProvider.php`

**使用：**
```blade
<!-- 快速格式化 -->
@currency($receivable->amount)

<!-- JavaScript 格式化函數 -->
<script>
@currencyJs
// 產生 formatCurrency() 函數
console.log(formatCurrency(210894)); // NT$ 210,894
</script>
```

## 📝 使用範例

### 已正確使用的頁面
✅ 應收帳款列表：`number_format($amount, 0)`
✅ 應付帳款列表：`number_format($amount, 0)`
✅ 專案詳情頁：`number_format($amount, 0)`

### 需要更新的頁面
⚠️ 財務報表圖表：已修復使用 `toLocaleString('zh-TW', {minimumFractionDigits: 0})`

### 新功能建議使用方式
```blade
<!-- Blade 模板 -->
<p>應收金額：@currency($receivable->amount)</p>

<!-- 或使用 helper -->
<p>應收金額：{{ format_currency($receivable->amount) }}</p>

<!-- JavaScript 中 -->
<script>
@currencyJs
document.getElementById('total').textContent = formatCurrency(210894);
</script>
```

## 🔄 向後兼容

**現有程式碼無需修改！**
- 目前使用 `number_format($amount, 0)` 的程式碼繼續正常運作
- 新功能提供更優雅的 API
- 未來支援多貨幣時自動適配

## 🎨 小數位數規則

### 無小數點貨幣 (0 位)
- TWD (新台幣)
- JPY (日圓)
- KRW (韓圜)
- VND (越南盾)
- IDR (印尼盾)

### 兩位小數貨幣 (2 位)
- USD (美金)
- EUR (歐元)
- GBP (英鎊)
- CNY (人民幣)
- HKD (港幣)
- 其他未列出的貨幣預設 2 位

## 📊 測試結果

```php
format_currency(210894)           → NT$ 210,894
format_currency(1234567.89, 'TWD') → NT$ 1,234,568
format_currency(210894, 'USD')     → US$ 210,894.00
format_currency(1234.5, 'USD')     → US$ 1,234.50
format_currency(10000, 'JPY')      → ¥ 10,000

currency_decimals('TWD') → 0
currency_decimals('USD') → 2
currency_decimals('JPY') → 0
```

## 🚀 未來擴展

### 多租戶貨幣設定
```php
// 從系統設定自動取得貨幣
$settings = Cache::get('tenant_settings_abc123', ['currency' => 'TWD']);
format_currency(210894); // 自動使用租戶設定的貨幣
```

### 前端統一格式化
```javascript
// 全站統一的 JS 格式化函數
@currencyJs

// 使用
formatCurrency(amount) // 自動根據系統貨幣設定格式化
```

## ✅ 完成項目

- [x] 建立 CurrencyHelper 類別
- [x] 建立全域 helper 函數
- [x] 註冊 Blade directives
- [x] 更新 composer autoload
- [x] 修復財務報表圖表格式化
- [x] 測試驗證功能正常

## 📝 注意事項

1. **資料庫儲存**仍使用精確的小數（decimal）
2. **顯示時**才根據貨幣決定格式
3. **計算時**使用原始數值，不受格式化影響
4. **現有程式碼**無需修改，向後兼容
