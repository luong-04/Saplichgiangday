<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Setting;
use Illuminate\Support\Collection;

class ScheduleService
{
    /**
     * Cache toàn bộ schedules để validate in-memory (tránh query DB nhiều lần).
     */
    private ?Collection $cachedSchedules = null;

    /**
     * Load toàn bộ lịch 1 lần vào memory.
     */
    public function loadSchedules(): Collection
    {
        if ($this->cachedSchedules === null) {
            $this->cachedSchedules = Schedule::all();
        }
        return $this->cachedSchedules;
    }

    /**
     * Set cached schedules từ bên ngoài (khi TimetableMatrix đã load sẵn).
     */
    public function setSchedules(Collection $schedules): void
    {
        $this->cachedSchedules = $schedules;
    }

    /**
     * Clear cache (sau khi thêm/xóa schedule).
     */
    public function clearCache(): void
    {
        $this->cachedSchedules = null;
    }

    /**
     * Validate toàn bộ ràng buộc trước khi xếp lịch.
     * Trả về string (lỗi) nếu vi phạm, false nếu hợp lệ.
     */
    public function validate($teacher_id, $class_id, $subject_id, $day, $period, $ignore_schedule_id = null)
    {
        $schedules = $this->loadSchedules();

        // Filter bỏ bản ghi đang sửa
        $filtered = $ignore_schedule_id
            ? $schedules->where('id', '!=', $ignore_schedule_id)
            : $schedules;

        // 1. Kiểm tra trùng lịch cơ bản
        $conflict = $this->checkConflict($filtered, $teacher_id, $class_id, $day, $period);
        if ($conflict)
            return $conflict;

        // 2. Lấy thông tin Subject và Teacher
        $subject = Subject::find($subject_id);
        $teacher = Teacher::find($teacher_id);

        if (!$subject || !$teacher) {
            return "Không tìm thấy thông tin môn học hoặc giáo viên.";
        }

        // 3. Kiểm tra tiết cố định (không cho đè lên Chào cờ / Sinh hoạt)
        $fixedCheck = $this->checkFixedPeriodConflict($filtered, $class_id, $day, $period);
        if ($fixedCheck)
            return $fixedCheck;

        // 4. Kiểm tra GV có rảnh không
        $availability = $this->checkTeacherAvailability($teacher, $day, $period);
        if ($availability)
            return $availability;

        // 5. Kiểm tra số tiết tối đa môn học / tuần
        $weeklyLimit = $this->checkSubjectWeeklyLimit($filtered, $subject, $class_id);
        if ($weeklyLimit)
            return $weeklyLimit;

        // 6. Kiểm tra số tiết tối đa môn học / ngày
        $dailySubjectLimit = $this->checkSubjectDailyLimit($filtered, $subject, $class_id, $day);
        if ($dailySubjectLimit)
            return $dailySubjectLimit;

        // 7. Kiểm tra số tiết tối đa GV / ngày
        $dailyTeacherLimit = $this->checkTeacherDailyLimit($filtered, $teacher, $day);
        if ($dailyTeacherLimit)
            return $dailyTeacherLimit;

        // 8. Kiểm tra định mức tiết/tuần GV
        $weeklyTeacherLimit = $this->checkTeacherWeeklyLimit($filtered, $teacher);
        if ($weeklyTeacherLimit)
            return $weeklyTeacherLimit;

        // 9. Cảnh báo tiết trống (không block, chỉ warning)
        // Sẽ trả về warning message hoặc false
        // Không block ở đây, TimetableMatrix sẽ xử lý hiển thị warning

        return false; // Tất cả đều hợp lệ
    }

    /**
     * Validate cho tiết đôi: kiểm tra cả tiết hiện tại + tiết kế liền.
     * Trả về ['error' => string] hoặc ['ok' => true, 'second_period' => int]
     */
    public function validateDoublePeriod($teacher_id, $class_id, $subject_id, $day, $period, $ignore_schedule_id = null): array
    {
        $subject = Subject::find($subject_id);
        if (!$subject || !$subject->is_double_period) {
            return ['error' => 'Môn này không phải tiết đôi.'];
        }

        $lunchAfter = 5;
        try {
            $lunchAfter = Setting::lunchAfterPeriod();
        }
        catch (\Exception $e) {
        }
        $periodsPerDay = 10;
        try {
            $periodsPerDay = Setting::periodsPerDay();
        }
        catch (\Exception $e) {
        }

        $secondPeriod = $period + 1;

        // Không cho tiết đôi vắt ngang qua giờ nghỉ trưa
        if ($period == $lunchAfter) {
            return ['error' => "Không thể xếp tiết đôi vắt qua giờ nghỉ trưa (tiết $period và $secondPeriod)."];
        }

        // Không cho tiết đôi vượt quá số tiết/ngày
        if ($secondPeriod > $periodsPerDay) {
            return ['error' => "Tiết $secondPeriod vượt quá số tiết trong ngày ($periodsPerDay)."];
        }

        // Validate tiết thứ 1
        $error1 = $this->validate($teacher_id, $class_id, $subject_id, $day, $period, $ignore_schedule_id);
        if ($error1)
            return ['error' => "Tiết $period: $error1"];

        // Validate tiết thứ 2
        $error2 = $this->validate($teacher_id, $class_id, $subject_id, $day, $secondPeriod, $ignore_schedule_id);
        if ($error2)
            return ['error' => "Tiết $secondPeriod: $error2"];

        return ['ok' => true, 'second_period' => $secondPeriod];
    }

    /**
     * Kiểm tra tiết trống của GV trong ngày.
     * Trả về warning message hoặc false.
     */
    public function checkTeacherGaps(Collection $schedules, Teacher $teacher, $day, $newPeriod): string|false
    {
        $maxGap = 2;
        try {
            $maxGap = Setting::maxGapPeriods();
        }
        catch (\Exception $e) {
        }

        // Lấy tất cả tiết GV dạy trong ngày + tiết mới
        $teacherPeriods = $schedules
            ->where('teacher_id', $teacher->id)
            ->where('day', $day)
            ->pluck('period')
            ->push($newPeriod)
            ->sort()
            ->values()
            ->toArray();

        if (count($teacherPeriods) < 2)
            return false;

        // Tìm khoảng trống lớn nhất
        $maxFoundGap = 0;
        for ($i = 1; $i < count($teacherPeriods); $i++) {
            $gap = $teacherPeriods[$i] - $teacherPeriods[$i - 1] - 1;

            // Bỏ qua khoảng trống do nghỉ trưa
            $lunchAfter = 5;
            try {
                $lunchAfter = Setting::lunchAfterPeriod();
            }
            catch (\Exception $e) {
            }
            if ($teacherPeriods[$i - 1] <= $lunchAfter && $teacherPeriods[$i] > $lunchAfter) {
                continue; // Khoảng trống do nghỉ trưa, bỏ qua
            }

            if ($gap > $maxFoundGap) {
                $maxFoundGap = $gap;
            }
        }

        if ($maxFoundGap > $maxGap) {
            return "⚠️ Cảnh báo: GV {$teacher->name} sẽ bị trống {$maxFoundGap} tiết giữa các ca dạy Thứ {$day}.";
        }

        return false;
    }

    /**
     * Kiểm tra tiết liên tiếp quá nhiều.
     */
    public function checkTeacherConsecutive(Collection $schedules, Teacher $teacher, $day, $newPeriod): string|false
    {
        $maxConsecutive = 4;
        try {
            $maxConsecutive = Setting::maxConsecutivePeriods();
        }
        catch (\Exception $e) {
        }

        $teacherPeriods = $schedules
            ->where('teacher_id', $teacher->id)
            ->where('day', $day)
            ->pluck('period')
            ->push($newPeriod)
            ->sort()
            ->values()
            ->toArray();

        if (count($teacherPeriods) <= $maxConsecutive)
            return false;

        // Đếm chuỗi tiết liên tiếp dài nhất
        $maxStreak = 1;
        $streak = 1;
        for ($i = 1; $i < count($teacherPeriods); $i++) {
            if ($teacherPeriods[$i] == $teacherPeriods[$i - 1] + 1) {
                $streak++;
                if ($streak > $maxStreak)
                    $maxStreak = $streak;
            }
            else {
                $streak = 1;
            }
        }

        if ($maxStreak > $maxConsecutive) {
            return "GV {$teacher->name} sẽ dạy {$maxStreak} tiết liên tiếp vào Thứ {$day} (tối đa cho phép: {$maxConsecutive}).";
        }

        return false;
    }

    // =========================================================================
    //  CÁC HÀM KIỂM TRA IN-MEMORY (trên Collection, không query DB)
    // =========================================================================

    /**
     * Kiểm tra trùng lịch in-memory.
     */
    private function checkConflict(Collection $schedules, $teacher_id, $class_id, $day, $period)
    {
        // GV trùng
        $teacherConflict = $schedules
            ->where('teacher_id', $teacher_id)
            ->where('day', $day)
            ->where('period', $period)
            ->first();

        if ($teacherConflict) {
            return "Giáo viên này đã có lịch dạy vào Thứ {$day} - Tiết {$period}.";
        }

        // Lớp trùng
        $classConflict = $schedules
            ->where('class_id', $class_id)
            ->where('day', $day)
            ->where('period', $period)
            ->first();

        if ($classConflict) {
            return "Lớp này đã có môn học vào Thứ {$day} - Tiết {$period}.";
        }

        return false;
    }

    /**
     * Kiểm tra không cho đè lên tiết cố định (Chào cờ, Sinh hoạt).
     */
    private function checkFixedPeriodConflict(Collection $schedules, $class_id, $day, $period)
    {
        $existing = $schedules
            ->where('class_id', $class_id)
            ->where('day', $day)
            ->where('period', $period)
            ->first();

        if ($existing) {
            $subject = Subject::find($existing->subject_id);
            if ($subject && $this->isFixedSubject($subject)) {
                return "Tiết {$period} Thứ {$day} là tiết cố định ({$subject->name}), không thể xếp đè lên.";
            }
        }

        return false;
    }

    /**
     * Kiểm tra apakah môn học là tiết cố định.
     */
    public function isFixedSubject(Subject $subject): bool
    {
        $name = mb_strtolower($subject->name);
        return str_contains($name, 'chào cờ')
            || str_contains($name, 'sinh hoạt');
    }

    /**
     * Kiểm tra GV có rảnh không.
     */
    private function checkTeacherAvailability(Teacher $teacher, $day, $period)
    {
        if (!$teacher->isAvailable((int)$day, (int)$period)) {
            return "GV {$teacher->name} không rảnh vào Thứ {$day} - Tiết {$period}.";
        }
        return false;
    }

    /**
     * Kiểm tra số tiết tối đa của môn học / tuần cho 1 lớp (in-memory).
     */
    private function checkSubjectWeeklyLimit(Collection $schedules, Subject $subject, $class_id)
    {
        $currentCount = $schedules
            ->where('subject_id', $subject->id)
            ->where('class_id', $class_id)
            ->count();

        $limit = $subject->lessons_per_week ?? 99;

        if ($currentCount >= $limit) {
            return "Môn {$subject->name} đã đủ {$limit} tiết/tuần cho lớp này.";
        }
        return false;
    }

    /**
     * Kiểm tra số tiết tối đa của môn học / ngày cho 1 lớp (in-memory).
     */
    private function checkSubjectDailyLimit(Collection $schedules, Subject $subject, $class_id, $day)
    {
        $currentCount = $schedules
            ->where('subject_id', $subject->id)
            ->where('class_id', $class_id)
            ->where('day', $day)
            ->count();

        $limit = $subject->max_lessons_per_day ?? 99;

        if ($currentCount >= $limit) {
            return "Môn {$subject->name} đã đạt tối đa {$limit} tiết/ngày (Thứ {$day}).";
        }
        return false;
    }

    /**
     * Kiểm tra số tiết tối đa GV / ngày (in-memory).
     */
    private function checkTeacherDailyLimit(Collection $schedules, Teacher $teacher, $day)
    {
        $currentCount = $schedules
            ->where('teacher_id', $teacher->id)
            ->where('day', $day)
            ->count();

        $limit = $teacher->max_periods_per_day ?? 5;

        if ($currentCount >= $limit) {
            return "GV {$teacher->name} đã dạy đủ {$limit} tiết Thứ {$day}.";
        }
        return false;
    }

    /**
     * Kiểm tra định mức tiết/tuần GV (in-memory).
     */
    private function checkTeacherWeeklyLimit(Collection $schedules, Teacher $teacher)
    {
        $currentCount = $schedules
            ->where('teacher_id', $teacher->id)
            ->count();

        $limit = $teacher->quota ?? 17;

        if ($currentCount >= $limit) {
            return "GV {$teacher->name} đã đạt định mức {$limit} tiết/tuần.";
        }
        return false;
    }
}