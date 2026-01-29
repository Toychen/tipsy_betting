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
