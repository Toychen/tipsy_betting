<?php

namespace App\Http\Controllers;

use App\Models\Gambling;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ramsey\Collection\Collection;

class GamblingController extends Controller
{
    /**
     * 顯示賭盤列表頁面
     * 路由: GET /gamblings
     */
    public function index()
    {
        // 取得所有賭盤，並預載關聯資料（避免 N+1 問題）

        return view('gamblings.index', ['gamblings' => collect()]);
    }

    /**
     * 顯示下注頁面
     * 路由: GET /gamblings/create
     */
    public function create()
    {
        // 取得所有成員供選擇

        return view('gamblings.create', ['members' => collect()]);
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

        return redirect()
                ->route('gamblings.index')
                ->with('success', '下注成功！');
    }

    /**
     * 顯示單一賭盤詳細資訊（選用）
     * 路由: GET /gamblings/{gambling}
     */
    public function show(Gambling $gambling)
    {
        $gambling->load(['papaMembers', 'lineMembers']);

        return view('gamblings.show', ['gambling' => $gambling]);
    }
}
