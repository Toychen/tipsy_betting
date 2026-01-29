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
