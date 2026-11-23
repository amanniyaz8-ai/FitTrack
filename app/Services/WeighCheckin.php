<?php

namespace App\Services;

use App\Models\Student;

/**
 * Раз в 2 недели бот просит вписать вес и сам поправляет калории:
 * похудение — худеешь медленнее половины плана → −150 ккал, быстрее плана в 1,5 раза → +100;
 * набор — вес не растёт → +150, растёт вдвое быстрее нормы → −150.
 * Поправка хранится в state.S.kadj и прибавляется к норме в конструкторе.
 */
class WeighCheckin
{
    public const EVERY_DAYS = 14;

    /** Кого пора спросить. */
    public static function due()
    {
        $edge = now()->subDays(self::EVERY_DAYS);

        return Student::whereNotNull('paid_at')->whereNotNull('onboarded_at')->get()->filter(function (Student $s) use ($edge) {
            $from = $s->weigh_at ?? $s->onboarded_at;
            if ($from->gt($edge) || empty($s->state['f']['weight'])) {
                return false;
            }
            // Уже спросили и ждём ответа — повторяем не чаще раза в неделю
            return ! $s->weigh_asked_at || $s->weigh_asked_at->lt($from) || $s->weigh_asked_at->lte(now()->subDays(7));
        });
    }

    public static function askText(Student $s): string
    {
        $name = $s->first_name ? $s->first_name.', ' : '';

        return "⚖️ {$name}прошло 2 недели — пора проверить, как идёт.\n\n"
            ."Встань на весы утром, натощак, после туалета, и пришли мне одно число — свой вес. Например: 84.5\n\n"
            .'Я сравню с планом и сам поправлю калории в конструкторе.';
    }

    /** Разобрать ответ. null — это не вес. */
    public static function parseKg(string $text): ?float
    {
        // «84,5», «84.5 кг», «вес 84,5», «актуальный вес: 84.5»
        if (! preg_match('/^\s*(?:[а-яёa-z ]{0,25}?вес\s*:?\s*)?(\d{2,3}(?:[.,]\d{1,2})?)\s*(кг|kg)?\.?\s*$/iu', $text, $m)) {
            return null;
        }
        $kg = (float) str_replace(',', '.', $m[1]);

        return $kg >= 30 && $kg <= 250 ? $kg : null;
    }

    public const MIN_DAYS = 10;

    /** Применить вес, вернуть текст ответа. */
    public static function apply(Student $s, float $kg): string
    {
        $st = $s->state ?? [];
        $S = $st['S'] ?? [];
        $f = $st['f'] ?? [];
        // Точка отсчёта: прошлое «зачётное» взвешивание, а до него — вес из анкеты
        if ($s->weigh_kg === null) {
            $s->weigh_kg = (float) ($f['weight'] ?? $kg);
            $s->weigh_at = $s->onboarded_at ?? $s->created_at ?? now();
        }
        $days = (int) floor($s->weigh_at->diffInDays(now()));
        if ($days < self::MIN_DAYS) {
            // Рано делать выводы — вес скачет от воды. Обновляем только вес (норма пересчитается под него).
            $f['weight'] = $kg;
            $st['f'] = $f;
            $st['savedAt'] = (int) (microtime(true) * 1000);
            $s->forceFill(['state' => $st])->save();
            $left = self::MIN_DAYS - $days;

            return "Записал: ".rtrim(rtrim(number_format($kg, 1, ',', ''), '0'), ',')." кг ✅\n\nНорму под новый вес конструктор уже пересчитал. Сравнить с планом и поправить калории смогу через {$left} ".self::dn($left).' — раньше вес скачет из-за воды, вывод был бы неточным.';
        }
        $prev = (float) $s->weigh_kg;
        $weeks = max(1, $days / 7);
        $perWeek = ($kg - $prev) / $weeks; // + набрал, − сбросил
        $goal = ($S['goal'] ?? 'Жиросжигание') === 'Набор массы' ? 'mass' : 'fat';
        $adj = 0;
        $fmt = fn ($x) => rtrim(rtrim(number_format(abs($x), 1, ',', ''), '0'), ',');

        if ($goal === 'fat') {
            $flKg = (float) ($f['fl_kg'] ?? 0);
            $flMo = (float) ($f['fl_mo'] ?? 0);
            $plan = $flKg > 0 && $flMo > 0 ? $flKg * 7 / ($flMo * 30.4) : $prev * 0.005;
            $lost = -$perWeek;
            if ($lost < $plan * 0.5) {
                $adj = -150;
                $verdict = 'Вес уходит медленнее плана. Убираю 150 ккал в день — это примерно один перекус.';
            } elseif ($lost > $plan * 1.5) {
                $adj = 100;
                $verdict = 'Худеешь быстрее плана — так можно потерять мышцы и сорваться. Добавляю 100 ккал в день.';
            } else {
                $verdict = 'Идёшь точно по плану 💪 Калории не меняю.';
            }
            $fact = $lost >= 0 ? '−'.$fmt($lost) : '+'.$fmt($lost);
            $line = "Было {$fmt($prev)} кг → стало {$fmt($kg)} кг. Это {$fact} кг в неделю, план — −{$fmt($plan)}.";
        } else {
            $hi = $prev * 0.015 / 4.3;
            if ($perWeek <= 0) {
                $adj = 150;
                $verdict = 'Вес не растёт — мышцам не хватает еды. Добавляю 150 ккал в день.';
            } elseif ($perWeek > $hi * 2) {
                $adj = -150;
                $verdict = 'Вес растёт слишком быстро — лишнее уходит в жир. Убираю 150 ккал в день.';
            } else {
                $verdict = 'Набираешь в хорошем темпе 💪 Калории не меняю.';
            }
            $line = "Было {$fmt($prev)} кг → стало {$fmt($kg)} кг. Это ".($perWeek >= 0 ? '+' : '−')."{$fmt($perWeek)} кг в неделю.";
        }

        $S['kadj'] = max(-600, min(600, (int) ($S['kadj'] ?? 0) + $adj));
        $f['weight'] = $kg;
        $st['S'] = $S;
        $st['f'] = $f;
        $st['savedAt'] = (int) (microtime(true) * 1000);

        $s->forceFill(['state' => $st, 'weigh_kg' => $kg, 'weigh_at' => now(), 'weigh_asked_at' => null])->save();

        return "{$line}\n\n{$verdict}\n\nКонструктор уже пересчитал — открой «Сколько есть». Следующий раз пришли вес через 10–14 дней.";
    }

    private static function dn(int $n): string
    {
        $a = $n % 10; $b = $n % 100;
        return ($a === 1 && $b !== 11) ? 'день' : (($a >= 2 && $a <= 4 && ($b < 12 || $b > 14)) ? 'дня' : 'дней');
    }
}
