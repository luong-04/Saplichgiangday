<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'short_code', 'lookup_code',
        'quota', 'homeroom_class_id',
        'max_periods_per_day', 'availability',
    ];

    protected $casts = [
        'availability' => 'array',
        'max_periods_per_day' => 'integer',
        'quota' => 'integer',
    ];

    // Một giáo viên có thể dạy nhiều môn
    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function homeroomClass()
    {
        return $this->belongsTo(ClassRoom::class , 'homeroom_class_id');
    }

    // Lịch dạy
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    /**
     * Kiểm tra giáo viên có rảnh ở thứ/tiết này không.
     * Nếu chưa cấu hình availability → mặc định rảnh tất cả.
     */
    public function isAvailable(int $day, int $period): bool
    {
        if (empty($this->availability)) {
            return true; // Chưa cấu hình = rảnh tất cả
        }

        $dayKey = (string)$day;
        if (!isset($this->availability[$dayKey])) {
            return false; // Ngày đó không có trong danh sách rảnh → bận
        }

        return in_array($period, $this->availability[$dayKey]);
    }

    /**
     * Tính số tiết còn lại.
     * Ưu tiên dùng schedules_count nếu đã eager-loaded (fix N+1).
     */
    public function getRemainingQuotaAttribute()
    {
        // Nếu đã eager-loaded withCount('schedules')
        if (isset($this->attributes['schedules_count'])) {
            $used = $this->attributes['schedules_count'];
        }
        else {
            $used = $this->schedules()->count();
        }
        return ($this->quota ?? 17) - $used;
    }
}