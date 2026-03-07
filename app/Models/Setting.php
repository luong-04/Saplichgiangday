<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'group'];

    /**
     * Lấy giá trị setting theo key, có cache.
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Set giá trị và clear cache.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    /**
     * Lấy tất cả settings theo group.
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember("settings_group_{$group}", 600, function () use ($group) {
            return static::where('group', $group)->pluck('value', 'key')->toArray();
        });
    }

    /**
     * Helper: lấy cấu hình timetable thường dùng.
     */
    public static function periodsPerDay(): int
    {
        return (int)static::get('periods_per_day', 10);
    }

    public static function daysStart(): int
    {
        return (int)static::get('days_start', 2);
    }

    public static function daysEnd(): int
    {
        return (int)static::get('days_end', 7);
    }

    public static function lunchAfterPeriod(): int
    {
        return (int)static::get('lunch_after_period', 5);
    }

    public static function maxConsecutivePeriods(): int
    {
        return (int)static::get('max_consecutive_periods', 4);
    }

    public static function maxGapPeriods(): int
    {
        return (int)static::get('max_gap_periods', 2);
    }

    public static function enforceRoomAssignment(): bool
    {
        return (bool)static::get('enforce_room_assignment', true);
    }
}
