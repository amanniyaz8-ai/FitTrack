<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class KonstruktorAccessTest extends TestCase
{
    use RefreshDatabase;

    public $mockConsoleOutput = false;

    private function fakeTelegram(string $status): void
    {
        Http::fake([
            'api.telegram.org/*getChatMember*' => Http::response(['ok' => true, 'result' => ['status' => $status]]),
            'api.telegram.org/*sendMessage*' => Http::response(['ok' => true]),
        ]);
    }

    private function start(int $id = 555)
    {
        return $this->postJson('/telegram/webhook', [
            'message' => ['text' => '/start konstruktor', 'chat' => ['id' => $id, 'type' => 'private'], 'from' => ['id' => $id, 'first_name' => 'Айдос', 'username' => 'aidos']],
        ], ['X-Telegram-Bot-Api-Secret-Token' => 's3cret']);
    }

    private function sentLink(): string
    {
        $link = null;
        Http::assertSent(function ($req) use (&$link) {
            if (str_contains($req->url(), 'sendMessage') && isset($req['reply_markup'])) {
                $link = json_decode($req['reply_markup'], true)['inline_keyboard'][0][0]['url'] ?? null;
            }
            return true;
        });

        return $link;
    }

    public function test_webhook_rejects_wrong_secret(): void
    {
        $this->postJson('/telegram/webhook', ['message' => []], ['X-Telegram-Bot-Api-Secret-Token' => 'nope'])->assertForbidden();
    }

    public function test_non_member_gets_sales_message_and_no_student(): void
    {
        $this->fakeTelegram('left');
        $this->start()->assertNoContent();
        $this->assertDatabaseCount('students', 0);
        $this->assertStringNotContainsString('/konstruktor/enter/', $this->sentLink());
    }

    public function test_member_gets_one_time_link_that_logs_in(): void
    {
        $this->fakeTelegram('member');
        $this->start()->assertNoContent();
        $this->assertDatabaseHas('students', ['telegram_id' => 555, 'first_name' => 'Айдос']);

        $link = $this->sentLink();
        $this->assertStringContainsString('/konstruktor/enter/', $link);
        $path = parse_url($link, PHP_URL_PATH);

        $this->get($path)->assertRedirect(route('konstruktor.show'));
        $this->get('/konstruktor')->assertOk()->assertSee('Конструктор тренировок', false)->assertSee('window.TP_API', false);

        // Ссылка одноразовая
        $this->post('/konstruktor/logout');
        $this->get($path)->assertRedirect(route('konstruktor.login'));
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

        $this->putJson('/konstruktor/state', ['S' => ['goal' => 'Сила', 'log' => ['x' => ['sets' => [['w' => '50', 'r' => '8']]]]], 'f' => ['name' => 'Айдос']])->assertNoContent();
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

    public function test_trainer_pages_still_work(): void
    {
        $this->get('/pricing')->assertOk();
        $this->get('/dashboard')->assertRedirect();
    }
}
