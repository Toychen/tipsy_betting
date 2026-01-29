# Laravel Workshop：尾牙賭盤下注系統 🎲

本專案是一個基於 Laravel 框架的教學範例，透過實作尾牙賭盤系統，帶領開發者完整學習 Laravel MVC 架構。

## 📚 專案說明

這是一個完整的 Laravel Workshop，透過實作尾牙賭盤下注系統，學習：

1. **DB Migration** - 資料庫結構設計與遷移
2. **Models** - 使用 Eloquent ORM 建立資料模型與關聯
3. **Controller** - 處理商業邏輯與資料流
4. **View (Blade)** - 使用 Blade 模板引擎建立使用者介面

最後完成一個可以下注和查看下注列表的完整系統。

## 🛠️ 技術棧

### 後端框架
- **Laravel** `^11.0` - PHP Web 應用框架
- **PHP** `^8.2` - 程式語言

### 前端技術
- **Blade** - Laravel 模板引擎
- **Bootstrap 5.3** - CSS 框架（透過 CDN）
- **Vanilla JavaScript** - 表單驗證與互動

### 資料庫
- **MySQL** / **PostgreSQL** / **SQLite** - 支援多種資料庫

### 開發工具
- **Composer** - PHP 套件管理工具
- **NPM** - JavaScript 套件管理工具（選用）

## 📋 版本依賴

確保你的開發環境符合以下需求：

```json
{
  "php": "^8.2",
  "laravel/framework": "^11.0",
  "composer": "^2.0"
}
```

**建議環境：**
- PHP 8.2 或以上
- Composer 2.x
- MySQL 8.0 或以上（或其他支援的資料庫）
- Node.js 18.x 或以上（選用，用於前端編譯）

## 🚀 快速開始

### 1. 安裝依賴

```bash
composer install
```

### 2. 環境設定

複製環境設定檔並設定資料庫連線：

```bash
cp .env.example .env
php artisan key:generate
```

編輯 `.env` 檔案，設定資料庫連線資訊：

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tipsy_betting
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. 執行 Migration

```bash
php artisan migrate
```

### 4. 建立測試資料（選用）

使用 Tinker 建立測試成員：

```bash
php artisan tinker
```

執行以下指令：

```php
$names = ['Alice', 'Bob', 'Charlie', 'David', 'Eve', 'Frank', 'Grace', 'Henry'];
foreach ($names as $name) {
    App\Models\Member::create(['name' => $name]);
}
exit
```

### 5. 啟動開發伺服器

```bash
php artisan serve
```

訪問 `http://localhost:8000` 開始使用！

## 📖 詳細教學文件

本專案提供完整的步驟說明文件，位於 `docs/` 資料夾：

1. **[專案總覽](docs/00-overview.md)** - 專案介紹與學習目標
2. **[Step 1: 資料庫 Migration](docs/01-database-migration.md)** - 建立資料表結構
3. **[Step 2: 建立 Models](docs/02-models.md)** - Eloquent ORM 與關聯設定
4. **[Step 3: 建立 Controller](docs/03-controller.md)** - 商業邏輯處理
5. **[Step 4: 建立 Views](docs/04-views.md)** - Blade 模板與前端介面

每個步驟都包含：
- 詳細的概念說明
- 完整的程式碼範例
- 重點觀念解析
- 測試方法
- 常見問題與解決方案

**建議新手從 [docs/00-overview.md](docs/00-overview.md) 開始閱讀！**

---

## 🎯 功能需求

### 資料表

#### members
| 欄位 | 型別 |
|------|------|
| id | bigint |
| name | string |
| created_at | timestamp |
| updated_at | timestamp |

#### gamblings
| 欄位 | 型別 |
|------|------|
| id | bigint |
| applicant | string |
| created_at | timestamp |
| updated_at | timestamp |

#### gambling_papa (Game1 明細)
| 欄位 | 型別 |
|------|------|
| gambling_id | bigint |
| member_id | bigint |

> Game1：可以勾選 5 個人

#### gambling_line (Game2 明細)
| 欄位 | 型別 |
|------|------|
| gambling_id | bigint |
| member_id | bigint |
| seq | tinyint |

> Game2：依序選擇 3 格人（seq = 1~3）

---

## 🕹️ 遊戲規則

### Game 1：尾牙斷線趴趴熊
- 勾選 **5 個人**
- 寫入 `gambling_papa`

### Game 2：尾牙醉失憶連線
- 依序選擇 **3 格人**
- 寫入 `gambling_line`，並記錄 `seq`

---

## 📄 頁面需求

### 1) 下注頁
路由：`GET /gamblings/create`

- 輸入 applicant（下注人）
- Game1 勾選 5 人
- Game2 依序選 3 人（第一格、第二格、第三格）
- 送出後建立一筆下注

### 2) 賭盤列表頁
路由：`GET /gamblings`

- 顯示每一筆下注紀錄
- Game1 選了哪 5 人
- Game2 seq1~3 各選了誰

---

## 📁 專案結構

```
tipsy_betting/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── GamblingController.php    # 賭盤控制器
│   └── Models/
│       ├── Member.php                     # 成員模型
│       └── Gambling.php                   # 賭盤模型
├── database/
│   └── migrations/
│       ├── *_create_members_table.php     # 成員表
│       ├── *_create_gamblings_table.php   # 賭盤表
│       ├── *_create_gambling_papa_table.php   # Game1 明細表
│       └── *_create_gambling_line_table.php   # Game2 明細表
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php              # 主版型
│       └── gamblings/
│           ├── index.blade.php            # 列表頁
│           └── create.blade.php           # 下注頁
├── routes/
│   └── web.php                            # Web 路由定義
├── docs/                                  # 📖 教學文件
│   ├── 00-overview.md
│   ├── 01-database-migration.md
│   ├── 02-models.md
│   ├── 03-controller.md
│   └── 04-views.md
└── README.md
```

## 🛣️ 路由列表

| HTTP 方法 | 路徑 | 控制器方法 | 路由名稱 | 說明 |
|----------|------|----------|---------|------|
| GET | `/` | - | - | 重新導向到賭盤列表 |
| GET | `/gamblings` | `index` | `gamblings.index` | 顯示賭盤列表 |
| GET | `/gamblings/create` | `create` | `gamblings.create` | 顯示下注表單 |
| POST | `/gamblings` | `store` | `gamblings.store` | 儲存下注資料 |

查看所有路由：
```bash
php artisan route:list
```

## 🎓 學習重點

透過本專案，你將學會：

### 1. 資料庫設計
- Migration 檔案的編寫
- 外鍵約束 (Foreign Key Constraints)
- 複合主鍵 (Composite Primary Key)
- 資料表關聯設計

### 2. Eloquent ORM
- Model 的建立與設定
- `belongsToMany` 多對多關聯
- 中間表 (Pivot Table) 的使用
- `withPivot()` 取得額外欄位
- Eager Loading 避免 N+1 問題

### 3. Controller 與路由
- Resource Controller 的使用
- 表單驗證 (Form Validation)
- 資料庫交易 (Database Transaction)
- Flash 訊息 (Session Flash)
- 路由命名與導向

### 4. Blade 模板
- 模板繼承 (`@extends`, `@yield`)
- 控制結構 (`@if`, `@foreach`, `@forelse`)
- 表單處理 (`@csrf`, `@error`)
- 舊輸入保留 (`old()`)
- Stack 功能 (`@push`, `@stack`)

## 🔧 常用指令

```bash
# 查看路由列表
php artisan route:list

# 進入 Tinker 互動環境
php artisan tinker

# 清除快取
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 重新執行 Migration（會清空資料！）
php artisan migrate:fresh

# 查看資料庫狀態
php artisan db:show
php artisan db:table members
```

## 🤝 貢獻

歡迎提交 Issue 或 Pull Request 來改進本教學專案！

## 📝 授權

本專案僅供教學使用。

## 📞 聯絡資訊

如有任何問題，請參考 [docs/](docs/) 資料夾中的詳細教學文件。

---

**開始你的 Laravel 學習之旅吧！** 🚀

建議從 **[docs/00-overview.md](docs/00-overview.md)** 開始閱讀。