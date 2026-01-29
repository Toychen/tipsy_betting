# Laravel Workshop：尾牙賭盤下注系統 - 專案總覽

## 專案簡介

這是一個完整的 Laravel Workshop，透過實作尾牙賭盤下注系統，帶領你學習 Laravel 的核心功能。

## 學習目標

透過這個 Workshop，你將學會：

1. **DB Migration** - 資料庫結構設計與遷移
2. **Models** - 使用 Eloquent ORM 建立資料模型
3. **Controller** - 處理商業邏輯與資料流
4. **View (Blade)** - 使用 Blade 模板引擎建立使用者介面

## 專案功能

### 遊戲規則

#### Game 1：尾牙斷線趴趴熊
- 每次下注可勾選 **5 個人**
- 資料儲存在 `gambling_papa` 表

#### Game 2：尾牙醉失憶連線
- 依序選擇 **3 格人**（第一格、第二格、第三格）
- 資料儲存在 `gambling_line` 表，並記錄順序 (seq)

## 頁面規劃

### 1. 下注頁面
- **路由**: `GET /gamblings/create`
- **功能**:
  - 輸入下注人姓名 (applicant)
  - Game1: 勾選 5 個人
  - Game2: 依序選擇 3 個人
  - 送出後建立一筆下注紀錄

### 2. 賭盤列表頁面
- **路由**: `GET /gamblings`
- **功能**:
  - 顯示所有下注紀錄
  - 顯示每筆下注的 applicant
  - 顯示 Game1 選了哪 5 個人
  - 顯示 Game2 第一格、第二格、第三格各選了誰

## 資料庫結構

### members (成員表)
| 欄位 | 型別 | 說明 |
|------|------|------|
| id | bigint | 主鍵 |
| name | string | 成員姓名 |
| created_at | timestamp | 建立時間 |
| updated_at | timestamp | 更新時間 |

### gamblings (賭盤主表)
| 欄位 | 型別 | 說明 |
|------|------|------|
| id | bigint | 主鍵 |
| applicant | string | 下注人姓名 |
| created_at | timestamp | 建立時間 |
| updated_at | timestamp | 更新時間 |

### gambling_papa (Game1 明細)
| 欄位 | 型別 | 說明 |
|------|------|------|
| gambling_id | bigint | 外鍵關聯 gamblings |
| member_id | bigint | 外鍵關聯 members |

> 複合主鍵：(gambling_id, member_id)

### gambling_line (Game2 明細)
| 欄位 | 型別 | 說明 |
|------|------|------|
| gambling_id | bigint | 外鍵關聯 gamblings |
| member_id | bigint | 外鍵關聯 members |
| seq | tinyint | 順序 (1~3) |

> 複合主鍵：(gambling_id, seq)

## Workshop 步驟

請依序完成以下步驟：

1. [資料庫 Migration](./01-database-migration.md)
2. [建立 Models](./02-models.md)
3. [建立 Controller](./03-controller.md)
4. [建立 Views](./04-views.md)

## 開發環境需求

- PHP >= 8.2
- Composer
- MySQL / PostgreSQL / SQLite
- Node.js & NPM (用於前端資源編譯)

## 開始之前

確保你已經：

1. 安裝好 Laravel 專案
2. 設定好 `.env` 檔案的資料庫連線
3. 執行 `composer install`
4. 執行 `php artisan key:generate`

準備好了嗎？讓我們從第一步開始吧！

[開始 Step 1：資料庫 Migration →](./01-database-migration.md)
