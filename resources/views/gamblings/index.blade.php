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
