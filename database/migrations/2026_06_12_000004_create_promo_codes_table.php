<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->unsignedTinyInteger('discount_percent');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('uses_count')->default(0);
            $table->unsignedInteger('max_uses')->nullable(); // null = unlimited
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        // Insert TeamNavoi promo code: 20% discount, unlimited, never expires
        DB::table('promo_codes')->insert([
            'code'             => 'TEAMNAVOI',
            'discount_percent' => 20,
            'is_active'        => true,
            'uses_count'       => 0,
            'max_uses'         => null,
            'expires_at'       => null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('promo_codes');
    }
};
