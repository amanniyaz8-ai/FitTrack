<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KonstruktorProfile extends Model
{
    protected $fillable = ['student_id', 'name', 'state'];

    protected $casts = ['state' => 'array'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
