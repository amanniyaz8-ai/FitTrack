<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('telegram:set-webhook', function (\App\Services\Telegram $tg) {
    $url = route('telegram.webhook');
    $res = $tg->setWebhook($url, (string) config('telegram.webhook_secret'));
    $this->info($url);
    $this->line(json_encode($res, JSON_UNESCAPED_UNICODE));
})->purpose('Подключить вебхук Telegram-бота конструктора');
