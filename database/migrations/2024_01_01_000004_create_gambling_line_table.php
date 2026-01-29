<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gambling_line', function (Blueprint $table) {
            $table->foreignId('gambling_id')->constrained('gamblings')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->tinyInteger('seq')->comment('順序：1~3');

            // 複合主鍵，確保同一個 gambling 的 seq 不會重複
            $table->primary(['gambling_id', 'seq']);

            // 索引，方便查詢
            $table->index(['gambling_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gambling_line');
    }
};
