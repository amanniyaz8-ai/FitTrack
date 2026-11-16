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
        $this->postJson('/konstruktor/telegram-auth', ['initData' => $this->initData(2)])->assertForbidden()->assertJson(['error' => 'not_member']);
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
        $this->get('/konstruktor')->assertRedirect(route('konstruktor.login'));
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

    public function test_left_channel_still_denied(): void
    {
        Http::fake(['api.telegram.org/*' => Http::response(['ok' => false, 'error_code' => 400, 'description' => 'Bad Request: user not found'], 400)]);
        $s = Student::create(['telegram_id' => 14, 'membership_checked_at' => now()->subDays(2)]);
        $this->withSession(['konstruktor_student_id' => $s->id])->get('/konstruktor')->assertRedirect();
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
}
