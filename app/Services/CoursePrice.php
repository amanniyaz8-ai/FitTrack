<?php

namespace App\Services;

use App\Models\CoursePurchase;

/**
 * Цена курса: первые N оплат после старта акции — по ранней цене, потом обычная.
 * Настройки в .env: COURSE_PRESALE_LIMIT, COURSE_PRESALE_FROM, COURSE_PRICE, COURSE_PRESALE_PRICE.
 */
class CoursePrice
{
    public static function limit(): int
    {
        return (int) config('course.presale_limit');
    }

    /** Сколько мест по ранней цене осталось. */
    public static function left(): int
    {
        try {
            $sold = CoursePurchase::where('status', 'approved')
                ->where('decided_at', '>=', config('course.presale_from'))
                ->count();
        } catch (\Throwable $e) {
            $sold = 0;
        }

        return max(0, self::limit() - $sold);
    }

    public static function isPresale(): bool
    {
        return self::left() > 0;
    }

    public static function amount(): int
    {
        return self::isPresale() ? (int) config('course.presale_price') : (int) config('course.price');
    }

    public static function text(): string
    {
        return number_format(self::amount(), 0, '', ' ').' ₸';
    }

    public static function regularText(): string
    {
        return number_format((int) config('course.price'), 0, '', ' ').' ₸';
    }

    /** «9 990 ₸ — осталось 3 из 5 мест, потом 15 990 ₸» или просто «15 990 ₸». */
    public static function line(): string
    {
        return self::isPresale()
            ? self::text().' — осталось '.self::left().' из '.self::limit().' мест, потом '.self::regularText()
            : self::text();
    }

    public static function payUrl(): string
    {
        $presale = config('course.pay_url_presale');

        return self::isPresale() && $presale ? $presale : (string) config('telegram.pay_url');
    }
}
