<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Восстанавливает отметки активности по уже сохранённым данным учеников (скачивание восстановить нельзя). */
return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('students')->get() as $s) {
            $state = json_decode((string) $s->state, true) ?: [];
            $when = $s->updated_at ?? $s->created_at;
            $sets = 0;
            foreach ((array) ($state['S']['log'] ?? []) as $ex) {
                foreach ((array) ($ex['sets'] ?? []) as $set) {
                    if (! empty($set['w']) || ! empty($set['r'])) {
                        $sets++;
                    }
                }
                if (! empty($ex['done'])) {
                    $sets++;
                }
            }
            $upd = [];
            if (! $s->first_open_at && ($s->last_login_at || $state)) {
                $upd['first_open_at'] = $s->last_login_at ?? $when;
            }
            if (! $s->onboarded_at && ! empty($state['touched'])) {
                $upd['onboarded_at'] = $when;
            }
            if (! $s->last_log_at && $sets > 0) {
                $upd['last_log_at'] = $when;
            }
            if ($upd) {
                DB::table('students')->where('id', $s->id)->update($upd);
            }
        }
    }

    public function down(): void {}
};
