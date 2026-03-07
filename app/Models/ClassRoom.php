<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = ['name', 'grade', 'lookup_code', 'shift'];

    // Giáo viên chủ nhiệm
    public function teacher()
    {
        return $this->hasOne(Teacher::class , 'homeroom_class_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class , 'class_id');
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