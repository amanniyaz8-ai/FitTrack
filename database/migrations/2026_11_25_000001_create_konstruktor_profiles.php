<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konstruktor_profiles', function (Blueprint $t) {
            $t->id();
            $t->foreignId('student_id')->constrained()->cascadeOnDelete();
            $t->string('name', 60);
            $t->json('state')->nullable();
            $t->timestamps();
        });
        Schema::table('students', function (Blueprint $t) {
            $t->boolean('is_trainer')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konstruktor_profiles');
        Schema::table('students', fn (Blueprint $t) => $t->dropColumn('is_trainer'));
    }
};
