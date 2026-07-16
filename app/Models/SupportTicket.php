<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $fillable = ['user_id', 'name', 'email', 'phone', 'message', 'status'];

    public function user() { return $this->belongsTo(\App\Models\User::class); }
}
