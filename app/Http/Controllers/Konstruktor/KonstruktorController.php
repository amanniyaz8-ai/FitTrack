<?php

namespace App\Http\Controllers\Konstruktor;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureStudent;
use App\Models\Student;
use App\Models\StudentLoginToken;
use App\Services\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class KonstruktorController extends Controller
{
    public function show(Request $request)
    {
        return view('konstruktor.page', ['student' => $request->attributes->get('student')]);
    }

    /** Одноразовая ссылка, чтобы открыть конструктор во внешнем браузере уже вошедшим (из Telegram там нельзя скачивать файлы). */
    public function browserLink(Request $request)
    {
        $token = $request->attributes->get('student')->issueLoginToken(30);

        return response()->json(['url' => route('konstruktor.enter', $token)]);
    }

    public function login()
    {
        return view('konstruktor.login', ['reason' => session('konstruktor_reason')]);
    }

    public function enter(Request $request, string $token)
    {
        $row = StudentLoginToken::where('token_hash', hash('sha256', $token))->first();

        if (! $row || $row->used_at || $row->expires_at->isPast()) {
            return redirect()->route('konstruktor.login')
                ->with('konstruktor_reason', 'Ссылка устарела или уже использована. Нажми /start в боте ещё раз — он пришлёт новую.');
        }

        $row->update(['used_at' => now()]);
        $this->loginStudent($request, $row->student);

        return redirect()->route('konstruktor.show');
    }

    /** Страница-прослойка для Telegram Mini App: получает initData и логинит. */
    public function app()
    {
        return view('konstruktor.app');
    }

    public function telegramAuth(Request $request, Telegram $tg)
    {
        $user = $tg->verifyInitData((string) $request->input('initData', ''));
        if (! $user) {
            return response()->json(['error' => 'bad_signature'], 403);
        }
        if (! $tg->isChannelMember((int) $user['id'])) {
            return response()->json(['error' => 'not_member'], 403);
        }

        $student = Student::updateOrCreate(
            ['telegram_id' => (int) $user['id']],
            ['telegram_username' => $user['username'] ?? null, 'first_name' => $user['first_name'] ?? null, 'membership_checked_at' => now()]
        );
        $this->loginStudent($request, $student);

        return response()->json(['ok' => true, 'redirect' => route('konstruktor.show')]);
    }

    private function loginStudent(Request $request, Student $student): void
    {
        $student->update(['last_login_at' => now()]);
        $request->session()->regenerate();
        $request->session()->put(EnsureStudent::SESSION_KEY, $student->id);
        Cookie::queue(EnsureStudent::COOKIE, (string) $student->id, 60 * 24 * config('telegram.remember_days'));
    }

    public function saveState(Request $request)
    {
        $student = $request->attributes->get('student');
        $raw = $request->getContent();

        if (strlen($raw) > 1_000_000) {
            return response()->json(['error' => 'too_large'], 413);
        }

        $data = json_decode($raw, true);
        if (! is_array($data) || ! isset($data['S']) || ! is_array($data['S'])) {
            return response()->json(['error' => 'bad_state'], 422);
        }

        $student->update(['state' => $data]);

        return response()->noContent();
    }

    public function logout(Request $request)
    {
        $request->session()->forget(EnsureStudent::SESSION_KEY);
        Cookie::queue(Cookie::forget(EnsureStudent::COOKIE));

        return redirect()->route('konstruktor.login');
    }
}
