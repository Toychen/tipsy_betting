# Step 2：建立 Models

## 什麼是 Model？

Model 是 Laravel 的 Eloquent ORM 的核心，它：
- 代表資料庫中的一個資料表
- 提供物件導向的方式操作資料
- 處理資料表之間的關聯關係
- 簡化資料的查詢、新增、更新、刪除

## 本步驟目標

建立 3 個 Eloquent Models：
1. `Member` - 對應 members 表
2. `Gambling` - 對應 gamblings 表
3. 設定 Model 之間的關聯關係

> 注意：`gambling_papa` 和 `gambling_line` 是中間表（pivot tables），通常不需要建立專屬的 Model

## 建立 Model 檔案

### 使用 Artisan 指令

```bash
# 建立 Member Model
php artisan make:model Member

# 建立 Gambling Model
php artisan make:model Gambling
```

執行後，Model 檔案會建立在 `app/Models/` 資料夾中。

---

## Model 實作

### 1. Member Model

**檔案位置**: `app/Models/Member.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    /**
     * 可以被批量賦值的欄位
     */
    protected $fillable = [
        'name',
    ];

    /**
     * 關聯：此成員在 Game1 被選擇的所有賭盤
     */
    public function gamblingsInPapa()
    {
        return $this->belongsToMany(
            Gambling::class,
            'gambling_papa',     // 中間表名稱
            'member_id',          // 中間表中此 model 的外鍵
            'gambling_id'         // 中間表中關聯 model 的外鍵
        );
    }

    /**
     * 關聯：此成員在 Game2 被選擇的所有賭盤
     */
    public function gamblingsInLine()
    {
        return $this->belongsToMany(
            Gambling::class,
            'gambling_line',
            'member_id',
            'gambling_id'
        )->withPivot('seq')      // 額外取得中間表的 seq 欄位
          ->orderBy('seq');       // 按照順序排序
    }
}
```

**重點說明**:
- `$fillable`: 定義哪些欄位可以被批量賦值（防止 Mass Assignment 漏洞）
- `belongsToMany()`: 多對多關聯
- `withPivot('seq')`: 取得中間表的額外欄位

---

### 2. Gambling Model

**檔案位置**: `app/Models/Gambling.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gambling extends Model
{
    use HasFactory;

    /**
     * 可以被批量賦值的欄位
     */
    protected $fillable = [
        'applicant',
    ];

    /**
     * 關聯：Game1 選擇的成員（最多 5 人）
     */
    public function papaMembers()
    {
        return $this->belongsToMany(
            Member::class,
            'gambling_papa',
            'gambling_id',
            'member_id'
        );
    }

    /**
     * 關聯：Game2 選擇的成員（3 人，有順序）
     */
    public function lineMembers()
    {
        return $this->belongsToMany(
            Member::class,
            'gambling_line',
            'gambling_id',
            'member_id'
        )->withPivot('seq')
          ->orderBy('seq');
    }

    /**
     * 取得 Game2 特定順序的成員
     *
     * @param int $seq 順序 (1, 2, 3)
     * @return Member|null
     */
    public function getLineMemberBySeq($seq)
    {
        return $this->lineMembers()
            ->wherePivot('seq', $seq)
            ->first();
    }

    /**
     * 取得 Game2 第一格的成員
     */
    public function getLineMember1()
    {
        return $this->getLineMemberBySeq(1);
    }

    /**
     * 取得 Game2 第二格的成員
     */
    public function getLineMember2()
    {
        return $this->getLineMemberBySeq(2);
    }

    /**
     * 取得 Game2 第三格的成員
     */
    public function getLineMember3()
    {
        return $this->getLineMemberBySeq(3);
    }
}
```

**重點說明**:
- 建立便利方法 `getLineMemberBySeq()` 來取得特定順序的成員
- 提供 `getLineMember1()`, `getLineMember2()`, `getLineMember3()` 方便在 View 中使用

---

## Eloquent 關聯類型

### belongsToMany（多對多）

```php
$this->belongsToMany(
    RelatedModel::class,    // 關聯的 Model
    'pivot_table',           // 中間表名稱
    'foreign_key',           // 此 Model 在中間表的外鍵
    'related_key'            // 關聯 Model 在中間表的外鍵
);
```

**使用情境**:
- 一個成員可以出現在多個賭盤中
- 一個賭盤可以選擇多個成員

### 其他常見關聯

```php
// 一對一
$this->hasOne(RelatedModel::class);
$this->belongsTo(RelatedModel::class);

// 一對多
$this->hasMany(RelatedModel::class);
$this->belongsTo(RelatedModel::class);
```

---

## 使用 Model 操作資料

### 建立資料

```php
// 方法 1：使用 create()
$member = Member::create([
    'name' => 'Alice'
]);

// 方法 2：使用 new + save()
$member = new Member();
$member->name = 'Bob';
$member->save();
```

### 查詢資料

```php
// 取得所有成員
$members = Member::all();

// 根據 ID 查詢
$member = Member::find(1);

// 條件查詢
$member = Member::where('name', 'Alice')->first();

// 取得多筆
$members = Member::where('name', 'like', 'A%')->get();
```

### 更新資料

```php
$member = Member::find(1);
$member->name = 'Alice Updated';
$member->save();

// 或使用 update()
$member->update(['name' => 'Alice Updated']);
```

### 刪除資料

```php
$member = Member::find(1);
$member->delete();

// 或直接刪除
Member::destroy(1);
Member::destroy([1, 2, 3]);
```

---

## 操作關聯資料

### 新增關聯

```php
// 建立一筆賭盤
$gambling = Gambling::create([
    'applicant' => 'John Doe'
]);

// Game1：關聯 5 個成員（只需要 member_id）
$gambling->papaMembers()->attach([1, 2, 3, 4, 5]);

// Game2：關聯 3 個成員（需要 member_id 和 seq）
$gambling->lineMembers()->attach([
    1 => ['seq' => 1],
    3 => ['seq' => 2],
    5 => ['seq' => 3],
]);
```

### 取得關聯資料

```php
// 取得賭盤
$gambling = Gambling::find(1);

// 取得 Game1 選擇的成員
$papaMembers = $gambling->papaMembers;  // Collection of Members

// 取得 Game2 選擇的成員
$lineMembers = $gambling->lineMembers;  // Collection of Members (已排序)

// 取得 Game2 第一格的成員
$firstMember = $gambling->getLineMember1();
echo $firstMember->name;

// 取得 Game2 所有成員及其順序
foreach ($gambling->lineMembers as $member) {
    echo "Seq {$member->pivot->seq}: {$member->name}";
}
```

### 更新關聯

```php
// 覆蓋所有關聯（會先移除舊的）
$gambling->papaMembers()->sync([2, 3, 4, 5, 6]);

// 移除特定關聯
$gambling->papaMembers()->detach(1);

// 移除所有關聯
$gambling->papaMembers()->detach();
```

---

## 測試 Model（使用 Tinker）

Laravel 提供 Tinker 工具讓你在終端機中測試 Model：

```bash
php artisan tinker
```

**測試範例**:

```php
// 建立成員
>>> $member = Member::create(['name' => 'Test User']);

// 建立賭盤
>>> $gambling = Gambling::create(['applicant' => 'John']);

// 關聯 Game1
>>> $gambling->papaMembers()->attach([1, 2, 3, 4, 5]);

// 關聯 Game2
>>> $gambling->lineMembers()->attach([
    1 => ['seq' => 1],
    2 => ['seq' => 2],
    3 => ['seq' => 3]
]);

// 查詢
>>> $gambling->papaMembers;
>>> $gambling->lineMembers;
>>> $gambling->getLineMember1()->name;

// 離開 Tinker
>>> exit
```

---

## 常見錯誤與解決

### 1. Mass Assignment Exception

**錯誤訊息**:
```
Illuminate\Database\Eloquent\MassAssignmentException
Add [name] to fillable property to allow mass assignment
```

**解決方法**: 在 Model 中新增 `$fillable` 屬性

```php
protected $fillable = ['name'];
```

### 2. Table Not Found

**錯誤訊息**:
```
SQLSTATE[42S02]: Base table or view not found
```

**解決方法**:
1. 確認已執行 Migration：`php artisan migrate`
2. 檢查 Model 的 `$table` 屬性（預設會使用複數形式的表名）

### 3. 關聯取不到資料

**檢查項目**:
1. 外鍵設定是否正確
2. 中間表名稱是否正確
3. 資料是否確實存在

---

## 完成檢查清單

- [ ] `Member` Model 已建立在 `app/Models/Member.php`
- [ ] `Gambling` Model 已建立在 `app/Models/Gambling.php`
- [ ] 兩個 Model 都設定了 `$fillable` 屬性
- [ ] 設定好 `belongsToMany` 關聯
- [ ] 使用 Tinker 測試過資料的建立與關聯
- [ ] 理解多對多關聯的運作方式

---

## 下一步

Models 建立完成！接下來我們要建立 Controller 來處理商業邏輯。

[← 返回 Step 1](./01-database-migration.md) | [前往 Step 3：建立 Controller →](./03-controller.md)
