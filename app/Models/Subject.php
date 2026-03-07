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
        'consecutive_periods',
        'preferred_room_category',
    ];

    protected $casts = [
        'is_double_period' => 'boolean',
        'lessons_per_week' => 'integer',
        'max_lessons_per_day' => 'integer',
        'consecutive_periods' => 'integer',
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }

    // Phòng chức năng được phép dùng cho môn này
    public function rooms()
    {
        return $this->belongsToMany(Room::class , 'room_subject');
    }

    /**
     * Tiết thực hành (type=2) cần phòng chức năng.
     */
    public function requiresRoom(): bool
    {
        return $this->type == '2'; // Thực hành
    }
}