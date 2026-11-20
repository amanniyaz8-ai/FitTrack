<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('week1_nudged_at')->nullable();
        });

        // Кто уже купил (одобренный чек) — сразу оплатившие.
        $paid = \Illuminate\Support\Facades\DB::table('course_purchases')->where('status', 'approved')->pluck('telegram_id');
        \Illuminate\Support\Facades\DB::table('students')->whereIn('telegram_id', $paid)->whereNull('paid_at')->update(['paid_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['paid_at', 'week1_nudged_at']);
        });
    }
};
