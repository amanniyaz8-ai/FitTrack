<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    /**
     * AJAX: validate a promo code and return discount info.
     */
    public function validate(Request $request)
    {

        $request->validate([
            'code' => 'required|string|max:50',
            'plan' => 'required|string|in:monthly,halfyear,annual',
        ]);

        $promo = PromoCode::findValid($request->code, $request->plan);

        if (!$promo) {
            return response()->json([
                'valid'   => false,
                'message' => 'Промокод недействителен или не применяется к этому тарифу.',
            ]);
        }

        return response()->json([
            'valid'            => true,
            'discount_percent' => $promo->discount_percent,
            'message'          => "Промокод применён: скидка {$promo->discount_percent}%",
        ]);
    }
}
