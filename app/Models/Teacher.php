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
        'teaching_shifts', 'assigned_classes'
    ];

    protected $casts = [
        'availability' => 'array',
        'teaching_shifts' => 'array',
        'assigned_classes' => 'array',
        'max_periods_per_day' => 'integer',
        'quota' => 'integer',
    ];

    /**
     * Tự động thêm remaining_quota vào JSON/array output
     * để Livewire không mất giá trị khi dehydrate.
     */
    protected $appends = ['remaining_quota'];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function homeroomClass()
    {
        return $this->belongsTo(ClassRoom::class , 'homeroom_class_id');
    }

    public function fixedPeriods()
    {
        return $this->hasMany(FixedPeriod::class , 'teacher_id');
    }

    public function assignments()
    {
        return $this->hasMany(TeacherAssignment::class , 'teacher_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function isAvailable(int $day, int $period): bool
    {
        if (empty($this->availability)) {
            return true;
        }
        $dayKey = (string)$day;
        if (!isset($this->availability[$dayKey])) {
            return false;
        }
        return in_array($period, $this->availability[$dayKey]);
    }

    public function canTeachShift(string $shift): bool
    {
        if (empty($this->teaching_shifts)) {
            return true;
        }
        return in_array($shift, $this->teaching_shifts);
    }

    /**
     * Tính tổng số tiết đã phân công (Logic mới dựa vào JSON).
     */
    public function calculateAssignedPeriods(): int
    {
        // Với logic mới, "assigned_classes" chỉ là mảng ID các lớp giáo viên được phép dạy.
        // Số tiết đã xếp thực tế được tính bằng getRemainingQuotaAttribute (giới hạn thực tế = số thẻ đã xếp).
        return $this->schedules()->count();
    }

    /**
     * Tính số tiết còn lại (dựa trên đã xếp). Ưu tiên schedules_count (withCount).
     */
    public function getRemainingQuotaAttribute()
    {
        if (isset($this->attributes['schedules_count'])) {
            $used = $this->attributes['schedules_count'];
        }
        else {
            $used = $this->schedules()->count();
        }
        return ($this->quota ?? 17) - $used;
    }
}