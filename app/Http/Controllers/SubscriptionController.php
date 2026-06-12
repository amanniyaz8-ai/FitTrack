<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    // Plan prices in KZT
    const PLANS = [
        'monthly'   => ['label' => '1 месяц',   'days' => 30,  'price' => 4990],
        'halfyear'  => ['label' => '6 месяцев', 'days' => 180, 'price' => 19990],
        'annual'    => ['label' => '1 год',     'days' => 365, 'price' => 23990],
    ];

    /**
     * Show pricing page.
     */
    public function pricing()
    {
        return view('subscription.pricing', ['plans' => self::PLANS]);
    }

    /**
     * Show checkout page for a specific plan (with optional promo code).
     */
    public function checkout(Request $request, string $plan)
    {
        if (!array_key_exists($plan, self::PLANS)) {
            abort(404);
        }

        $planData     = self::PLANS[$plan];
        $promoCode    = null;
        $finalPrice   = $planData['price'];
        $discountPct  = 0;

        if ($request->filled('promo')) {
            $promoCode = PromoCode::findValid($request->promo, $plan);
            if ($promoCode) {
                $discountPct = $promoCode->discount_percent;
                $finalPrice  = (int) round($planData['price'] * (1 - $discountPct / 100));
            }
        }

        return view('subscription.checkout', compact('plan', 'planData', 'promoCode', 'finalPrice', 'discountPct'));
    }
}
