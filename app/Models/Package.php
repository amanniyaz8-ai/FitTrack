<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'client_id',
        'total_sessions',
        'price',
        'payment_date',
        'is_paid',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'payment_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function getCompletedCountAttribute(): int
    {
        return $this->sessions()->where('status', 'completed')->count();
    }

    public function getMissedCountAttribute(): int
    {
        return $this->sessions()->where('status', 'missed')->count();
    }

    public function getRemainingCountAttribute(): int
    {
        return $this->total_sessions - $this->completed_count - $this->missed_count;
    }

    public function getIsArchivedAttribute(): bool
    {
        return $this->remaining_count <= 0;
    }

    public function getProgressPercentAttribute(): int
    {
        if ($this->total_sessions === 0) return 0;
        return (int) round(($this->completed_count / $this->total_sessions) * 100);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format((float) $this->price, 0, '.', ' ') . ' ₸';
    }
}
