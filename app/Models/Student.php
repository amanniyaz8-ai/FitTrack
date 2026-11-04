<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Student extends Model
{
    protected $fillable = ['telegram_id', 'telegram_username', 'first_name', 'state', 'membership_checked_at', 'last_login_at'];

    protected $casts = [
        'state' => 'array',
        'membership_checked_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function loginTokens(): HasMany
    {
        return $this->hasMany(StudentLoginToken::class);
    }

    /** Создаёт одноразовый токен входа и возвращает его открытое значение (в БД только хеш). */
    public function issueLoginToken(int $minutes = 30): string
    {
        $plain = Str::random(64);
        $this->loginTokens()->create([
            'token_hash' => hash('sha256', $plain),
            'expires_at' => now()->addMinutes($minutes),
        ]);

        return $plain;
    }
}
