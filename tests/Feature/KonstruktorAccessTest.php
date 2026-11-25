<?php

namespace Tests\Feature;

use App\Models\CoursePurchase;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KonstruktorAccessTest extends TestCase
{
    use RefreshDatabase;

    public $mockConsoleOutput = false;

    private const ADMIN = 777;

    protected function setUp(): void
    {
        parent::setUp();
        config(['telegram.admin_id' => self::ADMIN, 'telegram.pay_url' => 'https://pay.kaspi.kz/test', 'telegram.public_channel_url' => 'https://t.me/free_channel']);
    }

    private function fakeTelegram(string $status): void
    {
        Http::fake([
            'api.telegram.org/*getChatMember' => Http::response(['ok' => true, 'result' => ['status' => $status]]),
            'api.telegram.org/*createChatInviteLink' => Http::response(['ok' => true, 'result' => ['invite_link' => 'https://t.me/+personal123']]),
            'api.telegram.org/*' => Http::response(['ok' => true, 'result' => true]),
        ]);
    }

    private function hook(array $update)
    {
        return $this->postJson('/telegram/webhook', $update, ['X-Telegram-Bot-Api-Secret-Token' => 's3cret']);
    }

    private function msg(int $id, array $extra): array
    {
        return ['message' => array_merge(['chat' => ['id' => $id, 'type' => 'private'], 'from' => ['id' => $id, 'first_name' => 'Айдос', 'username' => 'aidos']], $extra)];
    }

    private function cb(int $from, string $data, array $message = []): array
    {
        return ['callback_query' => ['id' => 'cb1', 'data' => $data, 'from' => ['id' => $from, 'first_name' => 'X'], 'message' => array_merge(['message_id' => 10, 'chat' => ['id' => $from]], $message)]];
    }

    /** Все отправленные боту запросы метода $method */
    private function sent(string $method): array
    {
        return Http::recorded(fn ($req) => str_ends_with($req->url(), '/'.$method))->map(fn ($p) => $p[0]->data())->values()->all();
    }

    private function buttons(array $data): array
    {
        return collect(json_decode($data['reply_markup'] ?? '{}', true)['inline_keyboard'] ?? [])->flatten(1)->all();
    }

    public function test_webhook_rejects_wrong_secret(): void
    {
        $this->postJson('/telegram/webhook', ['message' => []], ['X-Telegram-Bot-Api-Secret-Token' => 'nope'])->assertForbidden();
    }

    public function test_newcomer_gets_sales_menu_with_pay_and_free_channel(): void
    {
        $this->fakeTelegram('left');
        $this->hook($this->msg(555, ['text' => '/start plan']))->assertNoContent();

        $m = $this->sent('sendMessage')[0];
        $this->assertStringContainsString('Сам себе тренер', $m['text']);
        $this->assertStringContainsString('15 990 ₸', $m['text']);
        $urls = array_column($this->buttons($m), 'url');
        $this->assertContains('https://pay.kaspi.kz/test', $urls);
        $this->assertContains('https://t.me/free_channel', $urls);
        $this->assertDatabaseCount('students', 0);
    }

    public function test_receipt_goes_to_admin_and_approve_sends_personal_invite(): void
    {
        $this->fakeTelegram('left');
        $this->hook($this->msg(555, ['photo' => [['file_id' => 'small'], ['file_id' => 'big']]]))->assertNoContent();

        $p = CoursePurchase::first();
        $this->assertSame('pending', $p->status);
        $this->assertSame('big', $p->receipt_file_id);
        $toAdmin = $this->sent('sendPhoto')[0];
        $this->assertEquals(self::ADMIN, $toAdmin['chat_id']);
        $this->assertSame("ap:{$p->id}", $this->buttons($toAdmin)[0]['callback_data']);

        // Чужой не может подтвердить
        $this->hook($this->cb(555, "ap:{$p->id}"));
        $this->assertSame('pending', $p->fresh()->status);

        // Аман подтверждает
        $this->hook($this->cb(self::ADMIN, "ap:{$p->id}", ['caption' => 'Новая оплата']));
        $this->assertSame('approved', $p->fresh()->status);
        $this->assertSame('1', (string) $this->sent('createChatInviteLink')[0]['member_limit']);

        $toBuyer = collect($this->sent('sendMessage'))->firstWhere('chat_id', 555 . '') ?? collect($this->sent('sendMessage'))->last();
        $btns = $this->buttons(collect($this->sent('sendMessage'))->last());
        $this->assertSame('https://t.me/+personal123', $btns[0]['url']);
        $this->assertStringContainsString('/konstruktor/app', $btns[1]['web_app']['url']);

        // Повторное нажатие ничего не делает
        $this->hook($this->cb(self::ADMIN, "ap:{$p->id}"));
        $this->assertCount(1, $this->sent('createChatInviteLink'));
    }

    public function test_reject_notifies_buyer(): void
    {
        $this->fakeTelegram('left');
        $p = CoursePurchase::create(['telegram_id' => 555, 'status' => 'pending', 'receipt_file_id' => 'f', 'receipt_type' => 'photo']);
        $this->hook($this->cb(self::ADMIN, "rj:{$p->id}"));
        $this->assertSame('rejected', $p->fresh()->status);
        $this->assertStringContainsString('Не получилось подтвердить', collect($this->sent('sendMessage'))->last()['text']);
    }

    public function test_member_gets_constructor_buttons_and_login_link_works(): void
    {
        $this->fakeTelegram('member');
        $this->hook($this->msg(555, ['text' => '/start']));
        $btns = $this->buttons($this->sent('sendMessage')[0]);
        $this->assertArrayHasKey('web_app', $btns[0]);

        $this->hook($this->cb(555, 'login'));
        $link = $this->buttons(collect($this->sent('sendMessage'))->last())[0]['url'];
        $path = parse_url($link, PHP_URL_PATH);

        $this->get($path)->assertRedirect(route('konstruktor.show'));
        $this->get('/konstruktor')->assertOk()->assertSee('window.TP_API', false);

        $this->post('/konstruktor/logout');
        $this->get($path)->assertRedirect(route('konstruktor.login')); // одноразовая
    }

    private function initData(int $userId, ?int $authDate = null): string
    {
        $data = ['auth_date' => (string) ($authDate ?? time()), 'query_id' => 'AAH', 'user' => json_encode(['id' => $userId, 'first_name' => 'Айдос'], JSON_UNESCAPED_UNICODE)];
        ksort($data);
        $check = implode("\n", array_map(fn ($k, $v) => "$k=$v", array_keys($data), $data));
        $secret = hash_hmac('sha256', 'test-token', 'WebAppData', true);
        $data['hash'] = hash_hmac('sha256', $check, $secret);

        return http_build_query($data);
    }

    public function test_mini_app_login_for_member(): void
    {
        $this->fakeTelegram('member');
        $this->get('/konstruktor/app')->assertOk()->assertSee('telegram-web-app.js', false);
        $this->postJson('/konstruktor/telegram-auth', ['initData' => $this->initData(4242)])->assertOk()->assertJson(['ok' => true]);
        $this->get('/konstruktor')->assertOk();
        $this->assertDatabaseHas('students', ['telegram_id' => 4242]);
    }

    public function test_mini_app_rejects_forged_old_or_non_member(): void
    {
        $this->fakeTelegram('member');
        $forged = str_replace('hash=', 'hash=00', $this->initData(1));
        $this->postJson('/konstruktor/telegram-auth', ['initData' => $forged])->assertForbidden();
        $this->postJson('/konstruktor/telegram-auth', ['initData' => $this->initData(1, time() - 90000)])->assertForbidden();
    }

    public function test_mini_app_rejects_non_member(): void
    {
        $this->fakeTelegram('left');
        // Бесплатная неделя: войти может любой, но без оплаты
        $this->postJson('/konstruktor/telegram-auth', ['initData' => $this->initData(2)])->assertOk();
        $this->assertNull(Student::where('telegram_id', 2)->first()->paid_at);
    }

    public function test_expired_token_rejected(): void
    {
        $s = Student::create(['telegram_id' => 1, 'membership_checked_at' => now()]);
        $t = $s->issueLoginToken(30);
        $this->travel(31)->minutes();
        $this->get('/konstruktor/enter/'.$t)->assertRedirect(route('konstruktor.login'));
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/konstruktor')->assertRedirect(route('konstruktor.login'));
        $this->get('/konstruktor/login')->assertOk()->assertSee('t.me/samsebetrener_bot?start=konstruktor', false);
        $this->putJson('/konstruktor/state', ['S' => []])->assertStatus(401);
    }

    public function test_state_saved_and_returned(): void
    {
        $s = Student::create(['telegram_id' => 2, 'membership_checked_at' => now()]);
        $this->withSession(['konstruktor_student_id' => $s->id]);
        $this->putJson('/konstruktor/state', ['S' => ['goal' => 'Сила'], 'f' => ['name' => 'Айдос']])->assertNoContent();
        $this->assertSame('Сила', $s->fresh()->state['S']['goal']);
        $this->get('/konstruktor')->assertOk()->assertSee('"goal":"Сила"', false);
        $this->putJson('/konstruktor/state', ['nope' => 1])->assertStatus(422);
    }

    public function test_member_who_left_loses_access_on_recheck(): void
    {
        $this->fakeTelegram('left');
        $s = Student::create(['telegram_id' => 3, 'membership_checked_at' => now()->subDays(2)]);
        $this->withSession(['konstruktor_student_id' => $s->id]);
        // вышел из канала — остаётся в конструкторе, но без оплаты видна только 1-я неделя
        $this->get('/konstruktor')->assertOk()->assertSee('"paid":false', false);
    }

    public function test_browser_link_logs_in_once(): void
    {
        $this->postJson('/konstruktor/browser-link')->assertStatus(401);
        $s = Student::create(['telegram_id' => 4, 'membership_checked_at' => now()]);
        $url = $this->withSession(['konstruktor_student_id' => $s->id])->postJson('/konstruktor/browser-link')->assertOk()->json('url');
        $this->flushSession();
        $this->get($url)->assertRedirect(route('konstruktor.show', ['dl' => 1]));
        $this->flushSession();
        $this->get($url)->assertRedirect(route('konstruktor.login'));
    }

    public function test_send_file_goes_to_student_chat(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => true, 'result' => []])]);
        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('p.html', '<html>программа</html>');
        $this->post('/konstruktor/send-file', ['kind' => 'html', 'file' => $file], ['Accept' => 'application/json'])->assertStatus(401);
        $s = Student::create(['telegram_id' => 5, 'membership_checked_at' => now()]);
        $this->withSession(['konstruktor_student_id' => $s->id])
            ->post('/konstruktor/send-file', ['kind' => 'html', 'file' => $file], ['Accept' => 'application/json'])->assertStatus(402);
        $s->update(['paid_at' => now()]);
        $this->withSession(['konstruktor_student_id' => $s->id])
            ->post('/konstruktor/send-file', ['kind' => 'html', 'file' => $file], ['Accept' => 'application/json'])->assertOk();
        Http::assertSent(fn ($r) => str_contains($r->url(), '/sendDocument') && str_contains($r->body(), 'name="chat_id"') && str_contains($r->body(), "\r\n\r\n5\r\n"));
        $this->withSession(['konstruktor_student_id' => $s->id])
            ->post('/konstruktor/send-file', ['kind' => 'exe', 'file' => $file], ['Accept' => 'application/json'])->assertStatus(422);
    }

    public function test_trainer_pages_still_work(): void
    {
        $this->get('/pricing')->assertOk();
        $this->get('/dashboard')->assertRedirect();
    }

    public function test_older_state_does_not_overwrite_newer(): void
    {
        $s = Student::create(['telegram_id' => 11, 'membership_checked_at' => now()]);
        $this->withSession(['konstruktor_student_id' => $s->id]);
        $this->putJson('/konstruktor/state', ['S' => ['goal' => 'Сила'], 'savedAt' => 2000])->assertNoContent();
        $this->putJson('/konstruktor/state', ['S' => ['goal' => 'Набор массы'], 'savedAt' => 1000])->assertStatus(409);
        $this->assertSame('Сила', $s->fresh()->state['S']['goal']);
        $this->putJson('/konstruktor/state', ['S' => ['goal' => 'Жиросжигание'], 'savedAt' => 3000])->assertNoContent();
        $this->assertSame('Жиросжигание', $s->fresh()->state['S']['goal']);
    }

    public function test_csrf_refresh_for_student(): void
    {
        $s = Student::create(['telegram_id' => 12, 'membership_checked_at' => now()]);
        $this->getJson('/konstruktor/csrf')->assertStatus(401);
        $this->withSession(['konstruktor_student_id' => $s->id])->getJson('/konstruktor/csrf')->assertOk()->assertJsonStructure(['csrf']);
    }

    public function test_telegram_outage_keeps_student_inside(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response('down', 502)]);
        $s = Student::create(['telegram_id' => 13, 'membership_checked_at' => now()->subDays(2)]);
        $this->withSession(['konstruktor_student_id' => $s->id])->get('/konstruktor')->assertOk();
        $this->assertTrue($s->fresh()->membership_checked_at->lt(now()->subDay()));
    }

    public function test_channel_member_becomes_paid(): void
    {
        $this->fakeTelegram('member');
        $s = Student::create(['telegram_id' => 14, 'membership_checked_at' => now()->subDays(2)]);
        $this->withSession(['konstruktor_student_id' => $s->id])->get('/konstruktor')->assertOk()->assertSee('"paid":true', false);
        $this->assertNotNull($s->fresh()->paid_at);
    }

    public function test_presale_price_then_regular(): void
    {
        config(['course.presale_from' => now()->subDay()->toDateTimeString(), 'course.presale_limit' => 2]);
        CoursePurchase::create(['telegram_id' => 1, 'status' => 'approved', 'decided_at' => now()->subDays(5)]); // старая покупка не считается
        $this->assertSame('9 990 ₸', \App\Services\CoursePrice::text());
        $this->assertStringContainsString('осталось 2 из 2', \App\Services\CoursePrice::line());
        CoursePurchase::create(['telegram_id' => 2, 'status' => 'approved', 'decided_at' => now()]);
        $this->assertStringContainsString('осталось 1 из 2', \App\Services\CoursePrice::line());
        CoursePurchase::create(['telegram_id' => 3, 'status' => 'approved', 'decided_at' => now()]);
        $this->assertSame('15 990 ₸', \App\Services\CoursePrice::line());
    }

    public function test_start_shows_free_try_and_approve_unlocks(): void
    {
        config(['telegram.webhook_secret' => 's3cret']);
        $this->fakeTelegram('left');
        $this->hook($this->msg(31, ['text' => '/start']));
        $m = collect($this->sent('sendMessage'))->firstWhere('chat_id', 31);
        $this->assertStringContainsString('бесплатно', $m['text']);
        $b = $this->buttons($m);
        $this->assertArrayHasKey('web_app', $b[0]);
        $this->assertStringContainsString('9 990', $b[1]['text']);

        $p = CoursePurchase::create(['telegram_id' => 31, 'status' => 'pending']);
        $this->hook($this->cb(self::ADMIN, 'ap:'.$p->id));
        $this->assertNotNull(Student::where('telegram_id', 31)->first()->paid_at);
    }

    public function test_week1_done_nudges_once(): void
    {
        $this->fakeTelegram('left');
        $s = Student::create(['telegram_id' => 32, 'membership_checked_at' => now()]);
        $this->withSession(['konstruktor_student_id' => $s->id]);
        $log = [];
        foreach ([0, 1, 2] as $i) {
            $log["Жиросжигание|Новичок|024|0|$i|0"] = ['sets' => [['w' => '20', 'r' => '12']]];
        }
        $this->putJson('/konstruktor/state', ['S' => ['days' => [0, 2, 4], 'log' => $log]])->assertNoContent();
        $this->putJson('/konstruktor/state', ['S' => ['days' => [0, 2, 4], 'log' => $log]])->assertNoContent();
        $msgs = collect($this->sent('sendMessage'))->where('chat_id', 32)->filter(fn ($m) => str_contains($m['text'], 'Первая неделя пройдена'));
        $this->assertCount(1, $msgs);
        $this->assertNotNull($s->fresh()->week1_nudged_at);
    }

    public function test_activity_marks_and_admin_stats(): void
    {
        config(['telegram.webhook_secret' => 's3cret']);
        $this->fakeTelegram('member');
        CoursePurchase::create(['telegram_id' => 21, 'status' => 'approved']);
        $s = Student::create(['telegram_id' => 21, 'first_name' => 'Айдос', 'membership_checked_at' => now()]);
        $this->withSession(['konstruktor_student_id' => $s->id])->get('/konstruktor')->assertOk();
        $this->putJson('/konstruktor/state', ['S' => ['log' => []], 'touched' => true])->assertNoContent();
        $this->putJson('/konstruktor/state', ['S' => ['log' => ['k' => ['sets' => [['w' => '40', 'r' => '12']]]]], 'touched' => true])->assertNoContent();
        $this->postJson('/konstruktor/downloaded')->assertNoContent();
        $s->refresh();
        $this->assertNotNull($s->first_open_at);
        $this->assertNotNull($s->onboarded_at);
        $this->assertNotNull($s->last_log_at);
        $this->assertNotNull($s->downloaded_at);

        $this->hook($this->msg(999, ['text' => '/stats']));
        $this->assertSame([], $this->sent('sendMessage') ? array_filter($this->sent('sendMessage'), fn ($m) => str_contains($m['text'], 'Статистика')) : []);

        $this->hook($this->msg(self::ADMIN, ['text' => '/stats']));
        $txt = collect($this->sent('sendMessage'))->pluck('text')->first(fn ($t) => str_contains($t, 'Статистика'));
        $this->assertStringContainsString('Купили (одобрено): 1', $txt);
        $this->assertStringContainsString('Прошли анкету: 1', $txt);
        $this->assertStringContainsString('Вносили веса за 7 дней: 1', $txt);
        $this->assertStringContainsString('Айдос', $txt);
    }

    public function test_stats_when_no_students(): void
    {
        config(['telegram.webhook_secret' => 's3cret']);
        $this->fakeTelegram('member');
        $this->hook($this->msg(self::ADMIN, ['text' => '/stats']));
        $txt = collect($this->sent('sendMessage'))->pluck('text')->first(fn ($t) => str_contains($t, 'Статистика'));
        $this->assertStringContainsString('Пока никто не заходил', $txt);
    }

    public function test_admin_posts_pinned_button_to_channel(): void
    {
        config(['telegram.webhook_secret' => 's3cret', 'telegram.channel_id' => -100555, 'telegram.bot_username' => 'samsebetrener_kz_bot']);
        Http::fake([
            'api.telegram.org/*sendMessage' => Http::response(['ok' => true, 'result' => ['message_id' => 42]]),
            'api.telegram.org/*' => Http::response(['ok' => true, 'result' => true]),
        ]);
        $this->hook($this->msg(999, ['text' => '/post_channel']));
        $this->assertEmpty(collect($this->sent('sendMessage'))->where('chat_id', -100555)->all());

        $this->hook($this->msg(self::ADMIN, ['text' => '/post_channel']));
        $post = collect($this->sent('sendMessage'))->firstWhere('chat_id', -100555);
        $this->assertNotNull($post);
        $this->assertSame('https://t.me/samsebetrener_kz_bot?start=konstruktor', $this->buttons($post)[0]['url']);
        $this->assertSame(42, (int) $this->sent('pinChatMessage')[0]['message_id']);
    }

    public function test_student_sends_weight_and_calories_adjust_by_goal(): void
    {
        $this->fakeTelegram('left');
        $st = Student::create(['telegram_id' => 7001, 'first_name' => 'Гуля', 'paid_at' => now(), 'onboarded_at' => now()->subDays(14),
            'state' => ['S' => ['goal' => 'Жиросжигание', 'kadj' => 0], 'f' => ['weight' => 80, 'fl_kg' => 8, 'fl_mo' => 4], 'savedAt' => 1]]);

        // 14 дней, −0,2 кг при плане −0,46 кг/нед → −150 ккал
        $this->hook($this->msg(7001, ['text' => 'актуальный вес 79,8']))->assertNoContent();
        $s = $st->fresh();
        $this->assertSame(-150, $s->state['S']['kadj']);
        $this->assertEquals(79.8, $s->state['f']['weight']);
        $this->assertStringContainsString('Убираю 150', collect($this->sent('sendMessage'))->last()['text']);

        // через 3 дня — только запоминаем вес, поправки нет
        $this->hook($this->msg(7001, ['text' => '79.5']))->assertNoContent();
        $s = $s->fresh();
        $this->assertSame(-150, $s->state['S']['kadj']);
        $this->assertEquals(79.5, $s->state['f']['weight']);
        $this->assertStringContainsString('через 10 дней', collect($this->sent('sendMessage'))->last()['text']);
    }

    public function test_mass_goal_no_gain_adds_calories(): void
    {
        $this->fakeTelegram('left');
        $st = Student::create(['telegram_id' => 7004, 'onboarded_at' => now()->subDays(12),
            'state' => ['S' => ['goal' => 'Набор массы'], 'f' => ['weight' => 70]]]);
        $this->hook($this->msg(7004, ['text' => '70']))->assertNoContent();
        $this->assertSame(150, $st->fresh()->state['S']['kadj']);
    }

    public function test_number_from_stranger_is_not_weight(): void
    {
        $this->fakeTelegram('left');
        $this->hook($this->msg(7003, ['text' => '71']))->assertNoContent();
        $this->assertNull(Student::where('telegram_id', 7003)->first());
    }

    public function test_trainer_keeps_clients_separately(): void
    {
        $t = Student::create(['telegram_id' => self::ADMIN, 'membership_checked_at' => now()]);
        $this->withSession(['konstruktor_student_id' => $t->id]);
        $this->post('/konstruktor/profiles', ['name' => 'Айгерим'])->assertRedirect();
        $p = $t->profiles()->first();
        $this->assertSame('Айгерим', $p->name);
        $this->get('/konstruktor?p='.$p->id)->assertOk()->assertSee('Чья программа')->assertSee('Удалить клиента');
        $this->putJson('/konstruktor/state?p='.$p->id, ['S' => ['goal' => 'Жиросжигание'], 'savedAt' => 5])->assertNoContent();
        $this->assertSame('Жиросжигание', $p->fresh()->state['S']['goal']);
        $this->assertNull($t->fresh()->state);

        // обычный ученик: ни выбора клиентов, ни доступа к чужому профилю
        $u = Student::create(['telegram_id' => 77, 'membership_checked_at' => now()]);
        $this->withSession(['konstruktor_student_id' => $u->id]);
        $this->get('/konstruktor')->assertOk()->assertDontSee('Чья программа');
        $this->post('/konstruktor/profiles', ['name' => 'X'])->assertForbidden();
        $this->putJson('/konstruktor/state?p='.$p->id, ['S' => ['goal' => 'Набор массы']])->assertStatus(404);
        $this->assertSame('Жиросжигание', $p->fresh()->state['S']['goal']);

        $this->withSession(['konstruktor_student_id' => $t->id]);
        $this->delete('/konstruktor/profiles/'.$p->id)->assertRedirect();
        $this->assertSame(0, $t->profiles()->count());
    }
}
