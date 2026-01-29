# Step 4：建立 Views (Blade 模板)

## 什麼是 Blade？

Blade 是 Laravel 的模板引擎，提供：
- 簡潔的語法
- 模板繼承（Layout）
- 元件（Component）
- 控制結構（if, foreach, etc.）
- 自動防範 XSS 攻擊

## 本步驟目標

建立 3 個 View 檔案：
1. **Layout 模板** - 共用的頁面結構
2. **下注頁面** - 顯示下注表單
3. **列表頁面** - 顯示所有下注紀錄

## 檔案結構

```
resources/views/
├── layouts/
│   └── app.blade.php          # 主版型
└── gamblings/
    ├── create.blade.php       # 下注頁面
    └── index.blade.php        # 列表頁面
```

---

## 1. 建立主版型（Layout）

**檔案位置**: `resources/views/layouts/app.blade.php`

```blade
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', '尾牙賭盤系統')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- 自訂樣式 -->
    <style>
        body {
            padding-top: 20px;
            padding-bottom: 40px;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .game-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .member-checkbox {
            margin: 5px 0;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- 導覽列 -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('gamblings.index') }}">🎲 尾牙賭盤系統</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('gamblings.index') }}">賭盤列表</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('gamblings.create') }}">我要下注</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- 主要內容 -->
    <div class="container mt-4">
        <!-- Flash 訊息 -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- 頁面內容 -->
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
```

---

## 2. 建立下注頁面

**檔案位置**: `resources/views/gamblings/create.blade.php`

```blade
@extends('layouts.app')

@section('title', '我要下注 - 尾牙賭盤系統')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">🎲 我要下注</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('gamblings.store') }}" method="POST" id="gamblingForm">
                    @csrf

                    <!-- 下注人姓名 -->
                    <div class="mb-4">
                        <label for="applicant" class="form-label fw-bold">下注人姓名 *</label>
                        <input
                            type="text"
                            class="form-control @error('applicant') is-invalid @enderror"
                            id="applicant"
                            name="applicant"
                            value="{{ old('applicant') }}"
                            placeholder="請輸入您的姓名"
                            required
                        >
                        @error('applicant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Game 1: 尾牙斷線趴趴熊 -->
                    <div class="game-section">
                        <h5 class="mb-3">🐻 Game 1：尾牙斷線趴趴熊</h5>
                        <p class="text-muted">請勾選 5 個人</p>

                        @error('papa_members')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <div class="row">
                            @foreach ($members as $member)
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-check member-checkbox">
                                        <input
                                            class="form-check-input papa-checkbox"
                                            type="checkbox"
                                            name="papa_members[]"
                                            value="{{ $member->id }}"
                                            id="papa_{{ $member->id }}"
                                            {{ in_array($member->id, old('papa_members', [])) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="papa_{{ $member->id }}">
                                            {{ $member->name }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-2">
                            <small class="text-muted">已選擇：<span id="papaCount">0</span> / 5</small>
                        </div>
                    </div>

                    <!-- Game 2: 尾牙醉失憶連線 -->
                    <div class="game-section">
                        <h5 class="mb-3">🍺 Game 2：尾牙醉失憶連線</h5>
                        <p class="text-muted">依序選擇 3 格人</p>

                        <div class="row">
                            <!-- 第一格 -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-info text-white">第一格</div>
                                    <div class="card-body">
                                        <select
                                            class="form-select @error('line_member_1') is-invalid @enderror"
                                            name="line_member_1"
                                            required
                                        >
                                            <option value="">-- 請選擇 --</option>
                                            @foreach ($members as $member)
                                                <option
                                                    value="{{ $member->id }}"
                                                    {{ old('line_member_1') == $member->id ? 'selected' : '' }}
                                                >
                                                    {{ $member->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('line_member_1')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- 第二格 -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-warning text-dark">第二格</div>
                                    <div class="card-body">
                                        <select
                                            class="form-select @error('line_member_2') is-invalid @enderror"
                                            name="line_member_2"
                                            required
                                        >
                                            <option value="">-- 請選擇 --</option>
                                            @foreach ($members as $member)
                                                <option
                                                    value="{{ $member->id }}"
                                                    {{ old('line_member_2') == $member->id ? 'selected' : '' }}
                                                >
                                                    {{ $member->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('line_member_2')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- 第三格 -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header bg-danger text-white">第三格</div>
                                    <div class="card-body">
                                        <select
                                            class="form-select @error('line_member_3') is-invalid @enderror"
                                            name="line_member_3"
                                            required
                                        >
                                            <option value="">-- 請選擇 --</option>
                                            @foreach ($members as $member)
                                                <option
                                                    value="{{ $member->id }}"
                                                    {{ old('line_member_3') == $member->id ? 'selected' : '' }}
                                                >
                                                    {{ $member->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('line_member_3')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 送出按鈕 -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            ✅ 確認下注
                        </button>
                        <a href="{{ route('gamblings.index') }}" class="btn btn-secondary">
                            返回列表
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Game1 勾選數量限制
    document.addEventListener('DOMContentLoaded', function() {
        const papaCheckboxes = document.querySelectorAll('.papa-checkbox');
        const papaCount = document.getElementById('papaCount');
        const maxPapa = 5;

        function updatePapaCount() {
            const checked = document.querySelectorAll('.papa-checkbox:checked').length;
            papaCount.textContent = checked;

            // 如果已選滿 5 個，禁用其他選項
            papaCheckboxes.forEach(checkbox => {
                if (!checkbox.checked && checked >= maxPapa) {
                    checkbox.disabled = true;
                } else {
                    checkbox.disabled = false;
                }
            });
        }

        papaCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updatePapaCount);
        });

        // 初始化計數
        updatePapaCount();

        // 表單送出驗證
        document.getElementById('gamblingForm').addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.papa-checkbox:checked').length;
            if (checked !== maxPapa) {
                e.preventDefault();
                alert(`Game1 請選擇 ${maxPapa} 個人（目前選了 ${checked} 個）`);
            }
        });
    });
</script>
@endpush
```

---

## 3. 建立列表頁面

**檔案位置**: `resources/views/gamblings/index.blade.php`

```blade
@extends('layouts.app')

@section('title', '賭盤列表 - 尾牙賭盤系統')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>🎲 賭盤列表</h2>
            <a href="{{ route('gamblings.create') }}" class="btn btn-primary">
                ➕ 我要下注
            </a>
        </div>

        @if ($gamblings->isEmpty())
            <div class="alert alert-info">
                目前還沒有任何下注紀錄，<a href="{{ route('gamblings.create') }}">立即下注</a>！
            </div>
        @else
            <div class="row">
                @foreach ($gamblings as $gambling)
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">{{ $gambling->applicant }}</span>
                                    <small>{{ $gambling->created_at->format('Y-m-d H:i') }}</small>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Game 1 -->
                                <div class="mb-3">
                                    <h6 class="fw-bold">🐻 Game 1：尾牙斷線趴趴熊</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @forelse ($gambling->papaMembers as $member)
                                            <span class="badge bg-info">{{ $member->name }}</span>
                                        @empty
                                            <span class="text-muted">無</span>
                                        @endforelse
                                    </div>
                                </div>

                                <!-- Game 2 -->
                                <div>
                                    <h6 class="fw-bold">🍺 Game 2：尾牙醉失憶連線</h6>
                                    <div class="row g-2">
                                        @php
                                            $lineMember1 = $gambling->getLineMember1();
                                            $lineMember2 = $gambling->getLineMember2();
                                            $lineMember3 = $gambling->getLineMember3();
                                        @endphp

                                        <div class="col-4">
                                            <div class="card bg-info text-white text-center">
                                                <div class="card-body py-2">
                                                    <small>第一格</small>
                                                    <div class="fw-bold">
                                                        {{ $lineMember1 ? $lineMember1->name : '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="card bg-warning text-dark text-center">
                                                <div class="card-body py-2">
                                                    <small>第二格</small>
                                                    <div class="fw-bold">
                                                        {{ $lineMember2 ? $lineMember2->name : '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-4">
                                            <div class="card bg-danger text-white text-center">
                                                <div class="card-body py-2">
                                                    <small>第三格</small>
                                                    <div class="fw-bold">
                                                        {{ $lineMember3 ? $lineMember3->name : '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-muted">
                                <small>下注編號：#{{ $gambling->id }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- 分頁（如果需要） -->
            {{-- {{ $gamblings->links() }} --}}
        @endif
    </div>
</div>
@endsection
```

---

## Blade 語法說明

### 1. 模板繼承

```blade
{{-- 子模板 --}}
@extends('layouts.app')

{{-- 定義區塊內容 --}}
@section('title', '頁面標題')

@section('content')
    <h1>內容</h1>
@endsection

{{-- 父模板 --}}
@yield('title', '預設標題')
@yield('content')
```

### 2. 顯示變數

```blade
{{-- 自動跳脫 HTML（防 XSS） --}}
{{ $variable }}

{{-- 不跳脫（危險！） --}}
{!! $html !!}

{{-- 三元運算子 --}}
{{ $name ?? 'Guest' }}
```

### 3. 控制結構

```blade
{{-- If 語句 --}}
@if ($count > 0)
    有資料
@elseif ($count === 0)
    沒有資料
@else
    未知
@endif

{{-- Unless --}}
@unless ($user->isAdmin())
    你不是管理員
@endunless

{{-- For 迴圈 --}}
@for ($i = 0; $i < 10; $i++)
    {{ $i }}
@endfor

{{-- Foreach 迴圈 --}}
@foreach ($members as $member)
    {{ $member->name }}
@endforeach

{{-- Forelse（處理空陣列）--}}
@forelse ($members as $member)
    {{ $member->name }}
@empty
    沒有成員
@endforelse
```

### 4. 表單相關

```blade
{{-- CSRF Token --}}
@csrf

{{-- HTTP Method Spoofing --}}
@method('PUT')
@method('DELETE')

{{-- 舊輸入（驗證失敗時保留） --}}
<input value="{{ old('name') }}">

{{-- 錯誤訊息 --}}
@error('name')
    <div>{{ $message }}</div>
@enderror
```

### 5. 引入其他檔案

```blade
{{-- 引入局部 View --}}
@include('partials.header')

{{-- 傳遞變數 --}}
@include('partials.user', ['user' => $user])

{{-- 條件引入 --}}
@includeIf('partials.sidebar')
@includeWhen($showSidebar, 'partials.sidebar')
```

### 6. Stack（堆疊）

```blade
{{-- 子模板中推入 --}}
@push('scripts')
    <script src="custom.js"></script>
@endpush

{{-- 父模板中顯示 --}}
@stack('scripts')
```

---

## JavaScript 互動功能

### Game1 勾選限制

```javascript
// 限制最多只能選 5 個
const maxPapa = 5;
const checked = document.querySelectorAll('.papa-checkbox:checked').length;

if (!checkbox.checked && checked >= maxPapa) {
    checkbox.disabled = true;
}
```

### 表單送出前驗證

```javascript
document.getElementById('gamblingForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.papa-checkbox:checked').length;
    if (checked !== 5) {
        e.preventDefault();
        alert('Game1 請選擇 5 個人');
    }
});
```

---

## Bootstrap 元件

本專案使用 Bootstrap 5 提供美觀的 UI：

### 常用元件

```blade
{{-- 卡片 --}}
<div class="card">
    <div class="card-header">標題</div>
    <div class="card-body">內容</div>
    <div class="card-footer">頁尾</div>
</div>

{{-- 按鈕 --}}
<button class="btn btn-primary">主要按鈕</button>
<button class="btn btn-secondary">次要按鈕</button>

{{-- 徽章 --}}
<span class="badge bg-info">標籤</span>

{{-- 警告訊息 --}}
<div class="alert alert-success">成功訊息</div>
<div class="alert alert-danger">錯誤訊息</div>

{{-- 表單 --}}
<div class="mb-3">
    <label class="form-label">標籤</label>
    <input class="form-control">
    <div class="invalid-feedback">錯誤訊息</div>
</div>
```

---

## 測試 Views

### 1. 準備測試資料

使用 Tinker 新增測試成員：

```bash
php artisan tinker
```

```php
// 批次新增成員
$names = ['Alice', 'Bob', 'Charlie', 'David', 'Eve', 'Frank', 'Grace', 'Henry'];
foreach ($names as $name) {
    App\Models\Member::create(['name' => $name]);
}
```

### 2. 啟動開發伺服器

```bash
php artisan serve
```

### 3. 測試頁面

- 列表頁：`http://localhost:8000/gamblings`
- 下注頁：`http://localhost:8000/gamblings/create`

### 4. 測試流程

1. 訪問下注頁
2. 輸入下注人姓名
3. Game1 勾選 5 個人
4. Game2 依序選擇 3 個人
5. 點擊「確認下注」
6. 確認導向列表頁並看到新下注紀錄

---

## 常見問題

### 1. View 找不到

**錯誤訊息**: `View [gamblings.index] not found`

**解決方法**:
- 確認檔案路徑正確：`resources/views/gamblings/index.blade.php`
- 確認檔案名稱包含 `.blade.php` 副檔名

### 2. 變數未定義

**錯誤訊息**: `Undefined variable: members`

**解決方法**:
- 確認 Controller 有傳遞變數：`return view('...', compact('members'));`
- 使用 `{{ $members ?? [] }}` 提供預設值

### 3. 樣式跑版

**解決方法**:
- 確認 Bootstrap CSS 正確載入
- 檢查瀏覽器開發者工具的 Console 是否有錯誤
- 清除瀏覽器快取

### 4. CSRF Token Mismatch

**錯誤訊息**: `419 | Page Expired`

**解決方法**:
- 確認表單中有 `@csrf`
- 檢查 session 是否正常運作

---

## 完成檢查清單

- [ ] `layouts/app.blade.php` 主版型已建立
- [ ] `gamblings/create.blade.php` 下注頁面已建立
- [ ] `gamblings/index.blade.php` 列表頁面已建立
- [ ] Bootstrap CSS/JS 正確載入
- [ ] Game1 勾選限制功能正常
- [ ] 表單驗證錯誤訊息正確顯示
- [ ] Flash 訊息（成功/失敗）正確顯示
- [ ] 所有頁面在瀏覽器中正常顯示

---

## 進階優化（選用）

### 1. 分頁功能

在 Controller 中：
```php
$gamblings = Gambling::with([...])
    ->orderBy('created_at', 'desc')
    ->paginate(10);  // 改用 paginate()
```

在 View 中：
```blade
{{ $gamblings->links() }}
```

### 2. 建立 Component

```bash
php artisan make:component GamblingCard
```

使用：
```blade
<x-gambling-card :gambling="$gambling" />
```

### 3. 響應式設計優化

為手機版調整欄位寬度：
```blade
<div class="col-md-3 col-sm-6 col-12">
```

---

## 恭喜完成！

你已經成功建立了完整的尾牙賭盤下注系統！

### 回顧學習內容

✅ **DB Migration** - 設計資料庫結構
✅ **Models** - 使用 Eloquent ORM
✅ **Controller** - 處理商業邏輯
✅ **Views** - 使用 Blade 模板

### 下一步建議

1. **新增功能**
   - 刪除下注紀錄
   - 編輯下注紀錄
   - 搜尋/篩選功能
   - 統計誰被選最多次

2. **優化體驗**
   - 加入 Ajax 送出表單（無需重新整理頁面）
   - 美化 UI（使用自訂 CSS）
   - 加入動畫效果

3. **學習進階主題**
   - Request 表單驗證類別
   - Resource Controllers
   - API 開發
   - 測試（PHPUnit）

[← 返回 Step 3](./03-controller.md) | [返回總覽](./00-overview.md)
