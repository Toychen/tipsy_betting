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
