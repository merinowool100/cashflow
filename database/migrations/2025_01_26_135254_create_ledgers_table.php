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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date')->after('user_id');
            $table->string('item')->after('date');
            $table->integer('amount')->after('item');
            $table->integer('balance')->default(0)->after('amount');  // 合計金額のカラム
            $table->boolean('repeat_monthly')->nullable()->after('balance');  // 毎月の繰り返し
            $table->boolean('repeat_yearly')->nullable()->after('repeat_monthly');  // 毎年の繰り返し
            $table->date('end_date')->nullable()->after('repeat_yearly');  // 繰り返しの期限
            $table->string('group_id')->nullable()->after('end_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
