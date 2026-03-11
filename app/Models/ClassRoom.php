<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = ['name', 'grade', 'lookup_code', 'shift', 'student_count', 'default_room_id'];

    // Phòng học mặc định
    public function defaultRoom()
    {
        return $this->belongsTo(Room::class , 'default_room_id');
    }

    // Giáo viên chủ nhiệm
    public function homeroomTeacher()
    {
        return $this->hasOne(Teacher::class , 'homeroom_class_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class , 'class_id');
    }

    public function fixedPeriods()
    {
        return $this->hasMany(FixedPeriod::class , 'class_id');
    }

    public function assignments()
    {
        return $this->hasMany(TeacherAssignment::class , 'class_id');
    }

    /**
     * Kiểm tra lớp thuộc buổi sáng.
     */
    public function isMorning(): bool
    {
        return in_array($this->shift, ['morning', 'both']);
    }

    /**
     * Kiểm tra lớp thuộc buổi chiều.
     */
    public function isAfternoon(): bool
    {
        return in_array($this->shift, ['afternoon', 'both']);
    }
}