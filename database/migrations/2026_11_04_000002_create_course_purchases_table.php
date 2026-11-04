<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_purchases', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('telegram_id')->index();
            $table->string('telegram_username')->nullable();
            $table->string('first_name')->nullable();
            $table->string('status', 16)->default('pending'); // pending | approved | rejected
            $table->string('receipt_file_id')->nullable();
            $table->string('receipt_type', 16)->nullable(); // photo | document
            $table->string('invite_link')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_purchases');
    }
};
