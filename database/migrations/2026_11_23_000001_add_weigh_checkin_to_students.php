<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $t) {
            $t->timestamp('weigh_asked_at')->nullable();
            $t->timestamp('weigh_at')->nullable();
            $t->decimal('weigh_kg', 5, 1)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $t) {
            $t->dropColumn(['weigh_asked_at', 'weigh_at', 'weigh_kg']);
        });
    }
};
