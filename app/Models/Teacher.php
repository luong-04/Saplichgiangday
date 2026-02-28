<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'short_code', 'lookup_code', 
        'quota', 'homeroom_class_id'
    ];

    // Một giáo viên có thể dạy nhiều môn
    public function subjects() {
        return $this->belongsToMany(Subject::class);
    }

    public function homeroomClass() {
        return $this->belongsTo(ClassRoom::class, 'homeroom_class_id');
    }

    // Logic tính số tiết còn lại (Quota - Số tiết đã xếp vào bảng schedules)
    public function getRemainingQuotaAttribute() {
        $used = Schedule::where('teacher_id', $this->id)->count();
        return ($this->quota ?? 17) - $used;
    }
}