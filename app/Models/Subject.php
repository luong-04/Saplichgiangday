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
        'consecutive_periods',
        'max_periods_per_day',
        'room_category_id',
    ];

    protected $casts = [
        'is_double_period' => 'boolean',
        'lessons_per_week' => 'integer',
        'max_lessons_per_day' => 'integer',
        'consecutive_periods' => 'integer',
        'max_periods_per_day' => 'integer',
    ];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class);
    }

    // Phòng chức năng được phép dùng cho môn này
    public function roomCategory()
    {
        return $this->belongsTo(RoomCategory::class , 'room_category_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class , 'subject_id');
    }

    public function fixedPeriods()
    {
        return $this->hasMany(FixedPeriod::class , 'subject_id');
    }

    public function curricula()
    {
        return $this->hasMany(Curriculum::class , 'subject_id');
    }

    public function assignments()
    {
        return $this->hasMany(TeacherAssignment::class , 'subject_id');
    }

    /**
     * Tiết thực hành (type=2) cần phòng chức năng.
     */
    public function requiresRoom(): bool
    {
        return $this->type == '2'; // Thực hành
    }
}