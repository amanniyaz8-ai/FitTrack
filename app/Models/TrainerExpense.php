<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerExpense extends Model
{
    protected $fillable = ['user_id', 'type', 'rent_amount', 'gym_percent'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
