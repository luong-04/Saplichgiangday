<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    // 1. Ép sử dụng đúng bảng classes trong database của bạn
    protected $table = 'classes';

    protected $fillable = ['name', 'grade'];

    // 2. Định nghĩa để lấy Giáo viên chủ nhiệm
    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'homeroom_class_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }
}