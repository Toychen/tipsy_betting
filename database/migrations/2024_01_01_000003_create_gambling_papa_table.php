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
        Schema::create('gambling_papa', function (Blueprint $table) {
            $table->foreignId('gambling_id')->constrained('gamblings')->onDelete('cascade');
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');

            // 複合主鍵，確保同一個 gambling 不會重複選擇同一個 member
            $table->primary(['gambling_id', 'member_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gambling_papa');
    }
};
