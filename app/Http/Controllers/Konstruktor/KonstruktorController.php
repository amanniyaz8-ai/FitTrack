<?php

namespace App\Http\Controllers\Konstruktor;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureStudent;
use App\Models\Student;
use App\Models\StudentLoginToken;
use App\Services\CoursePrice;
use App\Services\Telegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class KonstruktorController extends Controller
{
    public function show(Request $request)
    {
        $student = $request->attributes->get('student');
        if (! $student->first_open_at) {
            $this->mark($student, ['first_open_at' => now()]);
        }

        return view('konstruktor.page', ['student' => $student]);
    }

    /** Одноразовая ссылка, чтобы открыть конструктор во внешнем браузере уже вошедшим (из Telegram там нельзя скачивать файлы). */
    public function browserLink(Request $request)
    {
        $token = $request->attributes->get('student')->issueLoginToken(30);

        return response()->json(['url' => route('konstruktor.enter', ['token' => $token, 'dl' => 1])]);
    }

    /** Бот присылает ученику в чат файл программы или таблицы — удобнее, чем скачивать на телефоне. */
    public function sendFile(Request $request, Telegram $tg)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:6144'],
            'kind' => ['required', 'in:html,xlsx'],
        ]);
        $student = $request->attributes->get('student');
        if (! $student->isPaid()) {
            return response()->json(['error' => 'not_paid'], 402);
        }
        $name = ($data['kind'] === 'xlsx' ? 'Таблица тренировок' : 'Программа тренировок').'.'.$data['kind'];
        $caption = $data['kind'] === 'xlsx'
            ? 'Таблица тренировок. Открой в Google Таблицах или Excel — вписывай веса, рекомендации посчитаются сами.'
            : 'Программа тренировок на 12 недель. Открывается в браузере, можно распечатать.';
        $ok = $tg->sendFile((int) $student->telegram_id, $request->file('file')->get(), $name, $caption);
        if ($ok && ! $student->downloaded_at) {
            $this->mark($student, ['downloaded_at' => now()]);
        }

        return response()->json(['ok' => $ok], $ok ? 200 : 502);
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

        // Пришёл из Telegram за файлом — сразу покажем кнопки скачивания
        return redirect()->route('konstruktor.show', $request->boolean('dl') ? ['dl' => 1] : []);
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
        // Войти может любой из Telegram: без оплаты откроется бесплатная 1-я неделя.
        $member = $tg->channelMembership((int) $user['id']);
        $student = Student::updateOrCreate(
            ['telegram_id' => (int) $user['id']],
            ['telegram_username' => $user['username'] ?? null, 'first_name' => $user['first_name'] ?? null, 'membership_checked_at' => now()]
        );
        if ($member && ! $student->paid_at) {
            $this->mark($student, ['paid_at' => now()]);
        }
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

        // Старая копия (другое устройство, старая вкладка) не затирает более свежую.
        $old = is_array($student->state) ? (int) ($student->state['savedAt'] ?? 0) : 0;
        $new = (int) ($data['savedAt'] ?? 0);
        if ($old && $new && $new < $old) {
            return response()->json(['error' => 'stale', 'state' => $student->state], 409);
        }

        $marks = [];
        if (! $student->onboarded_at && ! empty($data['touched'])) {
            $marks['onboarded_at'] = now();
        }
        if ($this->filledSets($data) > $this->filledSets(is_array($student->state) ? $student->state : [])) {
            $marks['last_log_at'] = now();
        }
        $student->update(['state' => $data]);
        $this->mark($student, $marks);
        $this->nudgeAfterWeek1($student, $data);

        return response()->noContent();
    }

    /** Отметки активности. Если миграция ещё не запущена на сервере — молча пропускаем, работе ученика это не мешает. */
    private function mark($student, array $marks): void
    {
        if (! $marks) {
            return;
        }
        try {
            $student->forceFill($marks)->save();
        } catch (\Throwable $e) {
            $student->refresh();
            \Illuminate\Support\Facades\Log::warning('Student marks skipped: '.$e->getMessage());
        }
    }

    /** Бесплатный ученик прошёл 1-ю неделю — бот один раз предлагает открыть всю программу. */
    private function nudgeAfterWeek1(Student $student, array $data): void
    {
        if ($student->isPaid() || $student->week1_nudged_at) {
            return;
        }
        $sessions = [];
        foreach ((array) ($data['S']['log'] ?? []) as $key => $ex) {
            $p = explode('|', (string) $key);
            $n = count($p);
            if ($n < 3 || $p[$n - 3] !== '0') {
                continue;
            }
            $filled = ! empty($ex['done']) || collect((array) ($ex['sets'] ?? []))->contains(fn ($s) => ! empty($s['w']) || ! empty($s['r']));
            if ($filled) {
                $sessions[$p[$n - 2]] = true;
            }
        }
        $need = min(3, max(1, count((array) ($data['S']['days'] ?? [0, 2, 4]))));
        if (count($sessions) < $need) {
            return;
        }
        $this->mark($student, ['week1_nudged_at' => now()]);
        app(Telegram::class)->sendMessage((int) $student->telegram_id,
            "Первая неделя пройдена 💪\n\nДальше — 11 недель, которые программа уже подготовила под тебя: веса растут каждую неделю, разгрузки, 3 блока. Плюс видеокурс «Сам себе тренер».\n\n💰 ".CoursePrice::line()."\nВ Kaspi впиши сумму ".CoursePrice::text().", потом пришли сюда чек.", [
                [['text' => '💳 Открыть всю программу — '.CoursePrice::text(), 'url' => CoursePrice::payUrl()]],
                [['text' => config('bot_texts.btn_paid'), 'callback_data' => 'paid']],
            ]);
    }

    /** Сколько подходов ученик вписал (вес или повторы) — растёт, значит тренируется. */
    private function filledSets(array $state): int
    {
        $n = 0;
        foreach ((array) ($state['S']['log'] ?? []) as $ex) {
            foreach ((array) ($ex['sets'] ?? []) as $set) {
                if (! empty($set['w']) || ! empty($set['r'])) {
                    $n++;
                }
            }
            if (! empty($ex['done'])) {
                $n++;
            }
        }

        return $n;
    }

    /** Ученик скачал таблицу в браузере. */
    public function markDownload(Request $request)
    {
        $student = $request->attributes->get('student');
        if (! $student->downloaded_at) {
            $this->mark($student, ['downloaded_at' => now()]);
        }

        return response()->noContent();
    }

    /** Свежий CSRF-код, когда старый истёк, а приложение было долго открыто. */
    public function csrf(Request $request)
    {
        return response()->json(['csrf' => csrf_token()]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget(EnsureStudent::SESSION_KEY);
        Cookie::queue(Cookie::forget(EnsureStudent::COOKIE));

        return redirect()->route('konstruktor.login');
    }
}
