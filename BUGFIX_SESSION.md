# Phase 4 錯誤修復紀錄

## 2026-01-31 Session Driver 錯誤修復

### 問題描述
在多租戶環境中使用 `SESSION_DRIVER=database` 會導致錯誤：
```
SQLSTATE[42S02]: Base table or view not found: 1146 Table 'tenant_abc123_db.sessions' doesn't exist
```

### 原因分析
1. Session driver 設為 `database`
2. Session connection 配置為空（使用 default connection = central）
3. 租戶路由中，session 嘗試連接到租戶資料庫的 sessions 表
4. 但租戶資料庫中沒有 sessions 表（sessions 表應該在 central 資料庫）

### 解決方案
將 Session Driver 改為 `file`：

```bash
# .env
SESSION_DRIVER=file  # 原本是 database
```

### 優點
- ✅ 避免資料庫連線問題
- ✅ 每個租戶的 session 自動隔離（通過不同的 session ID）
- ✅ 效能較好（不需要資料庫查詢）
- ✅ 簡化配置

### 替代方案（如果需要 database driver）
如果必須使用 database driver，需要：
1. 在租戶資料庫中創建 sessions 表
2. 配置 session connection 動態切換到租戶連線
3. 或者將所有租戶的 session 統一存在 central 資料庫（但需要修改 session table 加入 tenant_id）

### 測試結果
✅ 專案列表頁面正常訪問
✅ 登入功能正常
✅ Session 正常儲存
