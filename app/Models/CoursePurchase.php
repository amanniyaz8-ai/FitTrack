<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePurchase extends Model
{
    protected $fillable = ['telegram_id', 'telegram_username', 'first_name', 'status', 'receipt_file_id', 'receipt_type', 'invite_link', 'decided_at'];

    protected $casts = ['decided_at' => 'datetime'];
}
