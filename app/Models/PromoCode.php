<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $fillable = [
        'code', 'discount_percent', 'is_active', 'uses_count', 'max_uses', 'expires_at', 'applicable_plans',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'expires_at'       => 'datetime',
        'applicable_plans' => 'array',
    ];

    public static function findValid(string $code, string $plan): ?self
    {
        $promo = self::where('code', strtoupper(trim($code)))
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('max_uses')->orWhereColumn('uses_count', '<', 'max_uses');
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if (!$promo || !$promo->appliesToPlan($plan)) {
            return null;
        }

        return $promo;
    }

    public function appliesToPlan(string $plan): bool
    {
        if (empty($this->applicable_plans)) {
            return true;
        }
        return in_array($plan, $this->applicable_plans);
    }

    public function incrementUses(): void
    {
        $this->increment('uses_count');
    }
}
