<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    protected $fillable = [
        'grade',
        'subject_id',
        'lessons_per_week',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
