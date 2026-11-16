<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->timestamp('first_open_at')->nullable();
            $table->timestamp('onboarded_at')->nullable();
            $table->timestamp('downloaded_at')->nullable();
            $table->timestamp('last_log_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['first_open_at', 'onboarded_at', 'downloaded_at', 'last_log_at']);
        });
    }
};
