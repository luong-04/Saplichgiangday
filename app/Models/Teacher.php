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
        'teaching_shifts',
    ];

    protected $casts = [
        'availability' => 'array',
        'teaching_shifts' => 'array',
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
     * Tính số tiết còn lại. Ưu tiên schedules_count (withCount).
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