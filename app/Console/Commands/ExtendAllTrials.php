<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ExtendAllTrials extends Command
{
    protected $signature = 'trial:extend-all {days=14}';
    protected $description = 'Extend trial or subscription for all active users by N days';

    public function handle()
    {
        $days = (int) $this->argument('days');

        $users = User::whereNotNull('trial_ends_at')->get();

        foreach ($users as $user) {
            $base = $user->subscription_ends_at && $user->subscription_ends_at->isFuture()
                ? $user->subscription_ends_at
                : now();

            $user->update(['subscription_ends_at' => $base->addDays($days)]);
        }

        $this->info("Доступ продлён на {$days} дней для {$users->count()} пользователей.");
    }
}
