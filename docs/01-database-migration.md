# Step 1：資料庫 Migration

## 什麼是 Migration？

Migration 是 Laravel 提供的資料庫版本控制系統，讓你可以：
- 定義資料表結構
- 與團隊成員共享資料庫結構
- 輕鬆回滾資料庫變更
- 確保開發、測試、正式環境的資料庫結構一致

## 本步驟目標

建立尾牙賭盤系統所需的 4 個資料表：
1. `members` - 成員表
2. `gamblings` - 賭盤主表
3. `gambling_papa` - Game1 明細表
4. `gambling_line` - Game2 明細表

## 建立 Migration 檔案

### 方法一：使用 Artisan 指令（推薦學習）

```bash
# 建立 members 表
php artisan make:migration create_members_table

# 建立 gamblings 表
php artisan make:migration create_gamblings_table

# 建立 gambling_papa 表
php artisan make:migration create_gambling_papa_table

# 建立 gambling_line 表
php artisan make:migration create_gambling_line_table
```

執行後，Laravel 會在 `database/migrations/` 資料夾中建立以時間戳記命名的檔案。

### 方法二：直接使用已建立好的檔案

本專案已經在 `database/migrations/` 資料夾中準備好所有 Migration 檔案。

## Migration 檔案說明

### 1. Members 表 (成員表)

**檔案位置**: `database/migrations/2024_01_01_000001_create_members_table.php`

```php
Schema::create('members', function (Blueprint $table) {
    $table->id();                    // 主鍵 (bigint, auto increment)
    $table->string('name');          // 成員姓名 (varchar 255)
    $table->timestamps();            // created_at 和 updated_at
});
```

**用途**: 儲存所有可以被選擇的成員資料

---

### 2. Gamblings 表 (賭盤主表)

**檔案位置**: `database/migrations/2024_01_01_000002_create_gamblings_table.php`

```php
Schema::create('gamblings', function (Blueprint $table) {
    $table->id();                    // 賭盤 ID (主鍵)
    $table->string('applicant');     // 下注人姓名
    $table->timestamps();            // 建立/更新時間
});
```

**用途**: 儲存每一筆下注的基本資料

---

### 3. Gambling Papa 表 (Game1 明細)

**檔案位置**: `database/migrations/2024_01_01_000003_create_gambling_papa_table.php`

```php
Schema::create('gambling_papa', function (Blueprint $table) {
    $table->foreignId('gambling_id')
        ->constrained('gamblings')    // 外鍵關聯 gamblings 表
        ->onDelete('cascade');        // 賭盤刪除時，明細也一併刪除

    $table->foreignId('member_id')
        ->constrained('members')      // 外鍵關聯 members 表
        ->onDelete('cascade');        // 成員刪除時，相關明細也刪除

    // 複合主鍵：確保同一個賭盤不會重複選擇同一個成員
    $table->primary(['gambling_id', 'member_id']);
});
```

**特色**:
- 使用複合主鍵 (gambling_id, member_id)
- 一個賭盤可以有多筆記錄（最多 5 筆）
- 同一個成員在同一個賭盤中不會重複出現

---

### 4. Gambling Line 表 (Game2 明細)

**檔案位置**: `database/migrations/2024_01_01_000004_create_gambling_line_table.php`

```php
Schema::create('gambling_line', function (Blueprint $table) {
    $table->foreignId('gambling_id')
        ->constrained('gamblings')
        ->onDelete('cascade');

    $table->foreignId('member_id')
        ->constrained('members')
        ->onDelete('cascade');

    $table->tinyInteger('seq')        // 順序：1, 2, 3
        ->comment('順序：1~3');

    // 複合主鍵：確保同一個賭盤的每個 seq 只能有一個成員
    $table->primary(['gambling_id', 'seq']);

    // 額外索引：提升查詢效能
    $table->index(['gambling_id', 'member_id']);
});
```

**特色**:
- 使用 `seq` 欄位記錄順序 (1, 2, 3)
- 複合主鍵 (gambling_id, seq) 確保每個格子只能選一個人
- 同一個成員可以在不同格子被選擇（例如第一格和第三格都選同一人）

---

## 執行 Migration

### 1. 檢查資料庫連線設定

編輯 `.env` 檔案，確認資料庫連線資訊正確：

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tipsy_betting
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 2. 執行 Migration

```bash
php artisan migrate
```

**執行結果範例**:
```
Migration table created successfully.
Migrating: 2024_01_01_000001_create_members_table
Migrated:  2024_01_01_000001_create_members_table (45.23ms)
Migrating: 2024_01_01_000002_create_gamblings_table
Migrated:  2024_01_01_000002_create_gamblings_table (38.67ms)
Migrating: 2024_01_01_000003_create_gambling_papa_table
Migrated:  2024_01_01_000003_create_gambling_papa_table (52.14ms)
Migrating: 2024_01_01_000004_create_gambling_line_table
Migrated:  2024_01_01_000004_create_gambling_line_table (49.82ms)
```

### 3. 驗證資料表已建立

**使用資料庫工具**（如 phpMyAdmin、TablePlus、DBeaver）查看資料庫，應該看到以下資料表：
- migrations
- members
- gamblings
- gambling_papa
- gambling_line

**或使用 Artisan 指令**:
```bash
php artisan db:show
php artisan db:table members
```

---

## 常用 Migration 指令

```bash
# 執行所有未執行的 migrations
php artisan migrate

# 回滾上一批 migrations
php artisan migrate:rollback

# 回滾所有 migrations
php artisan migrate:reset

# 回滾所有並重新執行（清空資料！）
php artisan migrate:fresh

# 回滾並重新執行，同時執行 seeders
php artisan migrate:fresh --seed

# 查看 migration 狀態
php artisan migrate:status
```

---

## 重點觀念

### 1. 外鍵約束 (Foreign Key Constraints)

```php
$table->foreignId('member_id')
    ->constrained('members')      // 自動關聯到 members.id
    ->onDelete('cascade');        // 刪除行為
```

**刪除行為選項**:
- `cascade` - 父資料刪除時，子資料也一併刪除
- `restrict` - 有子資料時，禁止刪除父資料
- `set null` - 父資料刪除時，子資料的外鍵設為 NULL
- `no action` - 不做任何動作

### 2. 複合主鍵 (Composite Primary Key)

```php
$table->primary(['gambling_id', 'member_id']);
```

用途：確保兩個欄位的組合是唯一的

### 3. 索引 (Index)

```php
$table->index(['gambling_id', 'member_id']);
```

用途：加快查詢速度，但會增加寫入時間

---

## 新增測試資料（選用）

你可以手動在資料庫新增一些測試成員：

```sql
INSERT INTO members (name, created_at, updated_at) VALUES
('Alice', NOW(), NOW()),
('Bob', NOW(), NOW()),
('Charlie', NOW(), NOW()),
('David', NOW(), NOW()),
('Eve', NOW(), NOW()),
('Frank', NOW(), NOW()),
('Grace', NOW(), NOW()),
('Henry', NOW(), NOW());
```

或等待後續步驟使用 Seeder 自動建立。

---

## 完成檢查清單

- [ ] 4 個 Migration 檔案已建立在 `database/migrations/` 資料夾
- [ ] `.env` 資料庫連線設定正確
- [ ] 執行 `php artisan migrate` 成功
- [ ] 資料庫中可以看到 4 個資料表
- [ ] 理解外鍵、複合主鍵、索引的用途

---

## 下一步

資料表結構建立完成！接下來我們要建立 Eloquent Models 來操作這些資料表。

[← 返回總覽](./00-overview.md) | [前往 Step 2：建立 Models →](./02-models.md)
