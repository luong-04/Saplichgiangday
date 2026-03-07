<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'lessons_per_week',
        'max_lessons_per_day',
        'is_double_period',
    ];

    protected $casts = [
        'is_double_period' => 'boolean',
        'lessons_per_week' => 'integer',
        'max_lessons_per_day' => 'integer',
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }
}