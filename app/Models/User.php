<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'specialization',
        'trial_ends_at',
        'subscription_ends_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'trial_ends_at'        => 'datetime',
            'subscription_ends_at' => 'datetime',
        ];
    }

    /**
     * True if the user currently has access (trial OR paid subscription).
     */
    public function hasActiveAccess(): bool
    {
        // NULL trial_ends_at = unrestricted (owner/admin account)
        if ($this->trial_ends_at === null && $this->subscription_ends_at === null) {
            return true;
        }
        if ($this->subscription_ends_at && now()->isBefore($this->subscription_ends_at)) {
            return true;
        }
        if ($this->trial_ends_at && now()->isBefore($this->trial_ends_at)) {
            return true;
        }
        return false;
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'trainer_id');
    }
}
