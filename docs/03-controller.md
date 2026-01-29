# Step 3：建立 Controller

## 什麼是 Controller？

Controller 是 MVC 架構中的 "C"（Controller），負責：
- 接收使用者的 HTTP 請求
- 處理商業邏輯
- 與 Model 互動（查詢、新增、更新、刪除資料）
- 將資料傳遞給 View
- 回傳 HTTP 回應

## 本步驟目標

建立 `GamblingController` 來處理：
1. 顯示下注頁面（create）
2. 處理下注表單送出（store）
3. 顯示賭盤列表頁面（index）

## 建立 Controller

### 使用 Artisan 指令

```bash
# 建立 Resource Controller（包含 CRUD 方法）
php artisan make:controller GamblingController --resource

# 或建立一般 Controller
php artisan make:controller GamblingController
```

Controller 會建立在 `app/Http/Controllers/` 資料夾中。

---

## Controller 實作

**檔案位置**: `app/Http/Controllers/GamblingController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Gambling;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GamblingController extends Controller
{
    /**
     * 顯示賭盤列表頁面
     * 路由: GET /gamblings
     */
    public function index()
    {
        // 取得所有賭盤，並預載關聯資料（避免 N+1 問題）
        $gamblings = Gambling::with(['papaMembers', 'lineMembers'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('gamblings.index', compact('gamblings'));
    }

    /**
     * 顯示下注頁面
     * 路由: GET /gamblings/create
     */
    public function create()
    {
        // 取得所有成員供選擇
        $members = Member::orderBy('name')->get();

        return view('gamblings.create', compact('members'));
    }

    /**
     * 處理下注表單送出
     * 路由: POST /gamblings
     */
    public function store(Request $request)
    {
        // 1. 驗證輸入
        $validated = $request->validate([
            'applicant' => 'required|string|max:255',
            'papa_members' => 'required|array|size:5',
            'papa_members.*' => 'exists:members,id',
            'line_member_1' => 'required|exists:members,id',
            'line_member_2' => 'required|exists:members,id',
            'line_member_3' => 'required|exists:members,id',
        ], [
            'applicant.required' => '請輸入下注人姓名',
            'papa_members.required' => 'Game1 請選擇 5 個人',
            'papa_members.size' => 'Game1 必須選擇 5 個人',
            'line_member_1.required' => 'Game2 請選擇第一格',
            'line_member_2.required' => 'Game2 請選擇第二格',
            'line_member_3.required' => 'Game2 請選擇第三格',
        ]);

        // 2. 使用交易確保資料一致性
        DB::beginTransaction();

        try {
            // 建立賭盤主記錄
            $gambling = Gambling::create([
                'applicant' => $validated['applicant']
            ]);

            // 關聯 Game1 的成員
            $gambling->papaMembers()->attach($validated['papa_members']);

            // 關聯 Game2 的成員（帶順序）
            $gambling->lineMembers()->attach([
                $validated['line_member_1'] => ['seq' => 1],
                $validated['line_member_2'] => ['seq' => 2],
                $validated['line_member_3'] => ['seq' => 3],
            ]);

            DB::commit();

            // 3. 重新導向到列表頁，並顯示成功訊息
            return redirect()
                ->route('gamblings.index')
                ->with('success', '下注成功！');

        } catch (\Exception $e) {
            DB::rollBack();

            // 發生錯誤時返回表單，並保留輸入資料
            return back()
                ->withInput()
                ->with('error', '下注失敗：' . $e->getMessage());
        }
    }

    /**
     * 顯示單一賭盤詳細資訊（選用）
     * 路由: GET /gamblings/{gambling}
     */
    public function show(Gambling $gambling)
    {
        $gambling->load(['papaMembers', 'lineMembers']);

        return view('gamblings.show', compact('gambling'));
    }
}
```

---

## 重點說明

### 1. 預載關聯資料（Eager Loading）

```php
$gamblings = Gambling::with(['papaMembers', 'lineMembers'])->get();
```

**為什麼需要？**
避免 N+1 查詢問題。

**不使用 with()**:
```php
// 1 次查詢取得所有賭盤
$gamblings = Gambling::all();

// 如果有 10 筆賭盤，會執行 20 次額外查詢！
foreach ($gamblings as $gambling) {
    $gambling->papaMembers;  // 1 次查詢
    $gambling->lineMembers;  // 1 次查詢
}
// 總共：1 + 10 + 10 = 21 次查詢
```

**使用 with()**:
```php
// 只需 3 次查詢
$gamblings = Gambling::with(['papaMembers', 'lineMembers'])->get();
// 1. 取得所有賭盤
// 2. 取得所有 papa_members
// 3. 取得所有 line_members
```

### 2. 表單驗證

```php
$validated = $request->validate([
    'applicant' => 'required|string|max:255',
    'papa_members' => 'required|array|size:5',
]);
```

**常用驗證規則**:
- `required` - 必填
- `string` - 必須是字串
- `max:255` - 最大長度 255
- `array` - 必須是陣列
- `size:5` - 陣列必須有 5 個元素
- `exists:members,id` - 值必須存在於 members 表的 id 欄位

**自訂錯誤訊息**:
```php
$request->validate($rules, [
    'applicant.required' => '請輸入下注人姓名',
]);
```

### 3. 資料庫交易（Transaction）

```php
DB::beginTransaction();
try {
    // 執行多個資料庫操作
    DB::commit();  // 全部成功才提交
} catch (\Exception $e) {
    DB::rollBack();  // 發生錯誤則全部回滾
}
```

**為什麼需要？**
確保資料一致性。如果建立賭盤成功，但關聯成員失敗，交易會自動回滾所有變更。

### 4. 重新導向與 Flash 訊息

```php
return redirect()
    ->route('gamblings.index')
    ->with('success', '下注成功！');
```

在 View 中顯示：
```blade
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
```

### 5. 路由模型綁定（Route Model Binding）

```php
public function show(Gambling $gambling)
{
    // Laravel 自動根據路由參數查詢 Gambling
    // 如果找不到會自動回傳 404
}
```

---

## 設定路由

**檔案位置**: `routes/web.php`

```php
<?php

use App\Http\Controllers\GamblingController;
use Illuminate\Support\Facades\Route;

// 方法 1：Resource 路由（推薦）
Route::resource('gamblings', GamblingController::class)
    ->only(['index', 'create', 'store']);

// 方法 2：手動定義路由
Route::get('/gamblings', [GamblingController::class, 'index'])
    ->name('gamblings.index');

Route::get('/gamblings/create', [GamblingController::class, 'create'])
    ->name('gamblings.create');

Route::post('/gamblings', [GamblingController::class, 'store'])
    ->name('gamblings.store');
```

**查看所有路由**:
```bash
php artisan route:list
```

**Resource 路由對照表**:
| HTTP 方法 | 路徑 | 方法 | 路由名稱 | 用途 |
|----------|------|------|---------|------|
| GET | /gamblings | index | gamblings.index | 列表頁 |
| GET | /gamblings/create | create | gamblings.create | 新增頁 |
| POST | /gamblings | store | gamblings.store | 儲存資料 |
| GET | /gamblings/{id} | show | gamblings.show | 詳細頁 |
| GET | /gamblings/{id}/edit | edit | gamblings.edit | 編輯頁 |
| PUT/PATCH | /gamblings/{id} | update | gamblings.update | 更新資料 |
| DELETE | /gamblings/{id} | destroy | gamblings.destroy | 刪除資料 |

---

## 測試 Controller

### 方法 1：使用瀏覽器

1. 啟動開發伺服器：
```bash
php artisan serve
```

2. 開啟瀏覽器訪問：
   - 列表頁：`http://localhost:8000/gamblings`
   - 下注頁：`http://localhost:8000/gamblings/create`

### 方法 2：使用 Artisan Tinker

```bash
php artisan tinker
```

```php
// 模擬請求
>>> $controller = new App\Http\Controllers\GamblingController();
>>> $request = new Illuminate\Http\Request();
>>> $controller->create();
```

### 方法 3：使用測試（進階）

```bash
php artisan make:test GamblingControllerTest
```

```php
public function test_can_create_gambling()
{
    $response = $this->post('/gamblings', [
        'applicant' => 'Test User',
        'papa_members' => [1, 2, 3, 4, 5],
        'line_member_1' => 1,
        'line_member_2' => 2,
        'line_member_3' => 3,
    ]);

    $response->assertRedirect('/gamblings');
    $this->assertDatabaseHas('gamblings', [
        'applicant' => 'Test User'
    ]);
}
```

---

## 常見錯誤與解決

### 1. Route Not Found

**錯誤訊息**: `Target class [GamblingController] does not exist.`

**解決方法**: 確認 Controller 的命名空間正確
```php
use App\Http\Controllers\GamblingController;
```

### 2. Validation Failed

**錯誤訊息**: `The given data was invalid.`

**解決方法**:
1. 檢查表單欄位名稱是否與驗證規則一致
2. 使用 `@error` directive 在 View 中顯示錯誤訊息

### 3. Mass Assignment Exception

**錯誤訊息**: `Add [applicant] to fillable property`

**解決方法**: 在 Model 中設定 `$fillable`
```php
protected $fillable = ['applicant'];
```

### 4. 關聯資料無法儲存

**檢查項目**:
1. 外鍵值是否正確
2. 中間表是否存在
3. 使用交易避免部分儲存成功的情況

---

## Request 與 Response

### 常用 Request 方法

```php
// 取得所有輸入
$request->all();

// 取得特定欄位
$request->input('applicant');
$request->applicant;  // 魔術方法

// 取得陣列
$request->input('papa_members', []);

// 判斷欄位是否存在
$request->has('applicant');

// 取得舊輸入（通常用於驗證失敗時）
old('applicant');
```

### 常用 Response 方法

```php
// 返回 View
return view('gamblings.index', compact('gamblings'));

// 重新導向
return redirect('/gamblings');
return redirect()->route('gamblings.index');
return redirect()->back();

// 返回 JSON（API）
return response()->json(['data' => $gamblings]);

// 下載檔案
return response()->download($pathToFile);
```

---

## 完成檢查清單

- [ ] `GamblingController` 已建立在 `app/Http/Controllers/`
- [ ] 實作 `index()` 方法顯示列表
- [ ] 實作 `create()` 方法顯示表單
- [ ] 實作 `store()` 方法處理表單送出
- [ ] 在 `routes/web.php` 中設定路由
- [ ] 使用 `php artisan route:list` 確認路由正確
- [ ] 理解 Eager Loading、驗證、交易的用途

---

## 下一步

Controller 建立完成！最後一步是建立 Views 來顯示使用者介面。

[← 返回 Step 2](./02-models.md) | [前往 Step 4：建立 Views →](./04-views.md)
