<?php

namespace App\Http\Middleware;

use App\Models\Student;
use App\Services\Telegram;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class EnsureStudent
{
    public const SESSION_KEY = 'konstruktor_student_id';
    public const COOKIE = 'konstruktor_student';

    public function __construct(private Telegram $tg) {}

    public function handle(Request $request, Closure $next)
    {
        // Cookie шифруется Laravel (EncryptCookies), подделать id нельзя.
        $id = $request->session()->get(self::SESSION_KEY) ?? $request->cookie(self::COOKIE);
        $student = $id ? Student::find($id) : null;

        if (! $student) {
            return $this->deny($request, null);
        }

        $hours = config('telegram.recheck_hours');
        // Неоплатившего проверяем чаще (раз в 10 минут) — вдруг уже вступил в канал после оплаты.
        $every = $student->paid_at ? now()->subHours($hours) : now()->subMinutes(10);
        if (! $student->membership_checked_at || $student->membership_checked_at->lt($every)) {
            // Конструктор открыт всем: без оплаты — бесплатная 1-я неделя. Участник закрытого канала считается оплатившим.
            // null — Telegram не ответил: ничего не меняем, проверим в следующий раз.
            $member = $this->tg->channelMembership((int) $student->telegram_id);
            if ($member !== null) {
                $upd = ['membership_checked_at' => now()];
                if ($member && ! $student->paid_at) {
                    $upd['paid_at'] = now();
                }
                try {
                    $student->update($upd);
                } catch (\Throwable $e) {
                    $student->update(['membership_checked_at' => now()]);
                }
            }
        }

        $request->session()->put(self::SESSION_KEY, $student->id);
        $request->attributes->set('student', $student);

        return $next($request);
    }

    private function deny(Request $request, ?string $reason)
    {
        if ($request->expectsJson() || $request->isMethod('put')) {
            return response()->json(['error' => 'unauthenticated'], 401);
        }

        return redirect()->route('konstruktor.login')->with('konstruktor_reason', $reason);
    }
}
