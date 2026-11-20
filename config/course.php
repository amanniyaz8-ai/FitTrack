<?php

return [
    'price' => (int) env('COURSE_PRICE', 15990),
    'presale_price' => (int) env('COURSE_PRESALE_PRICE', 9990),
    'presale_limit' => (int) env('COURSE_PRESALE_LIMIT', 5),
    // Ранние места считаются по оплатам, одобренным начиная с этой даты.
    'presale_from' => env('COURSE_PRESALE_FROM', '2026-11-20 00:00:00'),
    // Отдельная ссылка Kaspi на 9 990 ₸, если сумма в ссылке фиксированная. Пусто — общая COURSE_PAY_URL.
    'pay_url_presale' => env('COURSE_PAY_URL_PRESALE'),
];
