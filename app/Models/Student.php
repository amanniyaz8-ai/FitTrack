<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Student extends Model
{
    protected $fillable = ['telegram_id', 'telegram_username', 'first_name', 'state', 'membership_checked_at', 'last_login_at', 'first_open_at', 'onboarded_at', 'downloaded_at', 'last_log_at', 'paid_at', 'week1_nudged_at', 'weigh_asked_at', 'weigh_at', 'weigh_kg'];

    protected $casts = [
        'state' => 'array',
        'membership_checked_at' => 'datetime',
        'last_login_at' => 'datetime',
        'first_open_at' => 'datetime',
        'onboarded_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'last_log_at' => 'datetime',
        'paid_at' => 'datetime',
        'week1_nudged_at' => 'datetime',
        'weigh_asked_at' => 'datetime',
        'weigh_at' => 'datetime',
    ];

    /** Оплатил курс (одобренный чек или участник закрытого канала). Без оплаты открыта только 1-я неделя. */
    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

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
