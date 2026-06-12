<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trainer_id',
        'full_name',
        'phone',
        'goal',
        'contraindications',
        'training_days',
        'training_time',
    ];

    protected $casts = [
        'training_days' => 'array',
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function activePackage(): ?Package
    {
        return $this->packages()
            ->withCount(['sessions as completed_count' => fn($q) => $q->where('status', 'completed')])
            ->withCount(['sessions as missed_count' => fn($q) => $q->where('status', 'missed')])
            ->get()
            ->first(function ($pkg) {
                $done = $pkg->completed_count + $pkg->missed_count;
                return $done < $pkg->total_sessions;
            });
    }

    public function getTrainingDaysLabelAttribute(): string
    {
        $map = [
            'Mon' => 'Пн', 'Tue' => 'Вт', 'Wed' => 'Ср',
            'Thu' => 'Чт', 'Fri' => 'Пт', 'Sat' => 'Сб', 'Sun' => 'Вс',
        ];
        $days = $this->training_days ?? [];
        return implode(', ', array_map(fn($d) => $map[$d] ?? $d, $days));
    }
}
