<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\FixedPeriod;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Support\Collection;

class ScheduleService
{
    private ?Collection $cachedSchedules = null;

    public function loadSchedules(): Collection
    {
        if ($this->cachedSchedules === null) {
            $this->cachedSchedules = Schedule::all();
        }
        return $this->cachedSchedules;
    }

    public function setSchedules(Collection $schedules): void
    {
        $this->cachedSchedules = $schedules;
    }

    public function clearCache(): void
    {
        $this->cachedSchedules = null;
    }

    // =====================================================================
    //  VALIDATE TỔNG THỂ
    // =====================================================================

    public function validate($teacher_id, $class_id, $subject_id, $day, $period, $room_id = null, $ignore_schedule_id = null)
    {
        $schedules = $this->loadSchedules();
        $filtered = $ignore_schedule_id
            ? $schedules->where('id', '!=', $ignore_schedule_id)
            : $schedules;

        $subject = Subject::find($subject_id);
        $teacher = Teacher::find($teacher_id);
        $class = ClassRoom::find($class_id);

        if (!$subject || !$teacher || !$class) {
            return "Không tìm thấy thông tin môn học, giáo viên hoặc lớp.";
        }

        $c = $this->checkConflict($filtered, $teacher_id, $class_id, $day, $period);
        if ($c)
            return $c;

        $f = $this->checkFixedPeriodConflict($filtered, $class_id, $day, $period);
        if ($f)
            return $f;

        $a = $this->checkTeacherAvailability($teacher, $day, $period);
        if ($a)
            return $a;

        $s = $this->checkShiftIsolation($teacher, $class, $day, $period);
        if ($s)
            return $s;

        $wl = $this->checkSubjectWeeklyLimit($filtered, $subject, $class_id);
        if ($wl)
            return $wl;

        $dl = $this->checkSubjectDailyLimit($filtered, $subject, $class_id, $day);
        if ($dl)
            return $dl;

        $td = $this->checkTeacherDailyLimit($filtered, $teacher, $day);
        if ($td)
            return $td;

        $tw = $this->checkTeacherWeeklyLimit($filtered, $teacher);
        if ($tw)
            return $tw;

        $sp = $this->checkSubjectSpreading($filtered, $subject, $class_id, $day);
        if ($sp)
            return $sp;

        if ($room_id) {
            $rc = $this->checkRoomConflict($filtered, $room_id, $day, $period, $class_id, $subject_id);
            if ($rc)
                return $rc;
        }

        return false;
    }

    // =====================================================================
    //  TIẾT ĐÔI
    // =====================================================================

    public function validateMultiPeriod($teacher_id, $class_id, $subject_id, $day, $period, $room_id = null, $ignore_schedule_id = null, $consecutiveOverride = null): array
    {
        $subject = Subject::find($subject_id);
        if (!$subject) {
            return ['error' => 'Không tìm thấy môn học.'];
        }

        $consecutive = $consecutiveOverride ?? $subject->consecutive_periods ?? 1;

        $classSchedulesParams = Schedule::where('class_id', $class_id)->where('day', $day);
        if ($ignore_schedule_id) {
            $classSchedulesParams->where('id', '!=', $ignore_schedule_id);
        }
        $countDay = $classSchedulesParams->count();
        $countSubjectDay = $classSchedulesParams->get()->where('subject_id', $subject_id)->count();
        $limit = $subject->max_periods_per_day ?? $subject->max_lessons_per_day ?? 99;

        if ($countSubjectDay + $consecutive > $limit) {
            return ['error' => "Môn {$subject->name} đã đạt tối đa {$limit} tiết/ngày (Thứ {$day})."];
        }

        if ($consecutive <= 1) {
            $e1 = $this->validate($teacher_id, $class_id, $subject_id, $day, $period, $room_id, $ignore_schedule_id);
            if ($e1)
                return ['error' => "Tiết $period: $e1"];
            return ['ok' => true, 'periods' => [$period]];
        }

        $lunchAfter = 5;
        $periodsPerDay = 10;
        try {
            $lunchAfter = Setting::lunchAfterPeriod();
            $periodsPerDay = Setting::periodsPerDay();
        }
        catch (\Exception $e) {
        }

        $endPeriod = $period + $consecutive - 1;

        if ($endPeriod > $periodsPerDay) {
            return ['error' => "Tổng số tiết vượt quá giới hạn trong ngày ($periodsPerDay)."];
        }

        if ($period <= $lunchAfter && $endPeriod > $lunchAfter) {
            return ['error' => "Không thể xếp $consecutive tiết vắt qua giờ nghỉ trưa."];
        }

        $periods = [];
        for ($p = $period; $p <= $endPeriod; $p++) {
            $e = $this->validate($teacher_id, $class_id, $subject_id, $day, $p, $room_id, $ignore_schedule_id);
            if ($e)
                return ['error' => "Tiết $p: $e"];
            $periods[] = $p;
        }

        return ['ok' => true, 'periods' => $periods];
    }

    // =====================================================================
    //  CẢNH BÁO (KHÔNG BLOCK)
    // =====================================================================

    public function checkTeacherGaps(Collection $schedules, Teacher $teacher, $day, $newPeriod): string|false
    {
        $maxGap = 2;
        try {
            $maxGap = Setting::maxGapPeriods();
        }
        catch (\Exception $e) {
        }

        $teacherPeriods = $schedules
            ->where('teacher_id', $teacher->id)
            ->where('day', $day)
            ->pluck('period')
            ->push($newPeriod)
            ->sort()->values()->toArray();

        if (count($teacherPeriods) < 2)
            return false;

        $lunchAfter = 5;
        try {
            $lunchAfter = Setting::lunchAfterPeriod();
        }
        catch (\Exception $e) {
        }

        $maxFoundGap = 0;
        for ($i = 1; $i < count($teacherPeriods); $i++) {
            $gap = $teacherPeriods[$i] - $teacherPeriods[$i - 1] - 1;
            if ($teacherPeriods[$i - 1] <= $lunchAfter && $teacherPeriods[$i] > $lunchAfter) {
                continue;
            }
            if ($gap > $maxFoundGap)
                $maxFoundGap = $gap;
        }

        if ($maxFoundGap > $maxGap) {
            return "⚠️ GV {$teacher->name} sẽ bị trống {$maxFoundGap} tiết giữa các ca dạy Thứ {$day}.";
        }
        return false;
    }

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
            ->sort()->values()->toArray();

        if (count($teacherPeriods) <= $maxConsecutive)
            return false;

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
            return "⚠️ GV {$teacher->name} sẽ dạy {$maxStreak} tiết liên tiếp Thứ {$day} (max: {$maxConsecutive}).";
        }
        return false;
    }

    // =====================================================================
    //  TIẾT CỐ ĐỊNH — TỰ ĐỘNG GÁN
    // =====================================================================

    public function getFixedPeriodsForClass(ClassRoom $class): Collection
    {
        $allFixed = FixedPeriod::all();

        return $allFixed->filter(function ($fp) use ($class) {
            if ($fp->shift === 'morning' && $class->isMorning())
                return true;
            if ($fp->shift === 'afternoon' && $class->isAfternoon())
                return true;
            return false;
        });
    }

    /**
     * Tự động gán tiết cố định CHỈ khi lớp có GVCN.
     * Lớp chưa có GVCN → bỏ qua (khi gán GVCN sau sẽ tự điền).
     */
    public function autoAssignFixedPeriods(ClassRoom $class): int
    {
        $fixedPeriods = $this->getFixedPeriodsForClass($class);
        $gvcn = Teacher::where('homeroom_class_id', $class->id)->first();
        $count = 0;

        foreach ($fixedPeriods as $fp) {
            $existing = Schedule::where('class_id', $class->id)
                ->where('day', $fp->day)
                ->where('period', $fp->period)
                ->first();

            if ($existing)
                continue;

            // Lấy hoặc tạo Subject dựa trên tên trong bảng FixedPeriod
            $subject = Subject::where('name', $fp->subject_name)->first();
            if (!$subject) {
                $subject = Subject::create([
                    'name' => $fp->subject_name,
                    'is_fixed' => true,
                ]);
            }

            // Logic gán giáo viên: 
            // Nếu fp yêu cầu gán GVCN VÀ lớp có GVCN thì lấy ID, ngược lại để null
            $assignedTeacherId = ($fp->auto_assign_homeroom && $gvcn) ? $gvcn->id : null;

            Schedule::create([
                'teacher_id' => $assignedTeacherId,
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'day' => $fp->day,
                'period' => $fp->period,
            ]);

            $count++;
        }

        return $count;
    }

    // =====================================================================
    //  CÁC HÀM KIỂM TRA IN-MEMORY
    // =====================================================================

    private function checkConflict(Collection $schedules, $teacher_id, $class_id, $day, $period)
    {
        if ($schedules->where('teacher_id', $teacher_id)->where('day', $day)->where('period', $period)->first()) {
            return "Giáo viên này đã có lịch dạy vào Thứ {$day} - Tiết {$period}.";
        }
        if ($schedules->where('class_id', $class_id)->where('day', $day)->where('period', $period)->first()) {
            return "Lớp này đã có môn học vào Thứ {$day} - Tiết {$period}.";
        }
        return false;
    }

    private function checkFixedPeriodConflict(Collection $schedules, $class_id, $day, $period)
    {
        $existing = $schedules->where('class_id', $class_id)->where('day', $day)->where('period', $period)->first();
        if ($existing) {
            $subject = Subject::find($existing->subject_id);
            if ($subject && $this->isFixedSubject($subject)) {
                return "Tiết {$period} Thứ {$day} là tiết cố định ({$subject->name}), không thể xếp đè.";
            }
        }
        return false;
    }

    public function isFixedSubject(Subject $subject): bool
    {
        // Kiểm tra trực tiếp flag is_fixed trong database
        return (bool)$subject->is_fixed;
    }

    private function checkTeacherAvailability(Teacher $teacher, $day, $period)
    {
        if (!$teacher->isAvailable((int)$day, (int)$period)) {
            return "GV {$teacher->name} không rảnh vào Thứ {$day} - Tiết {$period}.";
        }
        return false;
    }

    private function checkShiftIsolation(Teacher $teacher, ClassRoom $class, $day, $period)
    {
        if (empty($teacher->teaching_shifts)) {
            return false;
        }

        $morningEnd = 5;
        $afternoonStart = 6;
        try {
            $morningEnd = (int)Setting::get('morning_end', 5);
            $afternoonStart = (int)Setting::get('afternoon_start', 6);
        }
        catch (\Exception $e) {
        }

        $periodShift = ($period <= $morningEnd) ? 'morning' : 'afternoon';

        if (!$teacher->canTeachShift($periodShift)) {
            $shiftLabel = $periodShift === 'morning' ? 'sáng' : 'chiều';
            return "GV {$teacher->name} chưa đăng ký dạy buổi {$shiftLabel}. Không thể xếp tiết {$period}.";
        }

        return false;
    }

    private function checkSubjectWeeklyLimit(Collection $schedules, Subject $subject, $class_id)
    {
        $class = ClassRoom::find($class_id);
        $curriculum = $class ?\App\Models\Curriculum::where('subject_id', $subject->id)->where('grade', $class->grade)->first() : null;
        $limit = $curriculum ? $curriculum->lessons_per_week : ($subject->lessons_per_week ?? 99);

        $count = $schedules->where('subject_id', $subject->id)->where('class_id', $class_id)->count();
        if ($count >= $limit) {
            return "Môn {$subject->name} đã đủ {$limit} tiết/tuần cho lớp này.";
        }
        return false;
    }

    private function checkSubjectDailyLimit(Collection $schedules, Subject $subject, $class_id, $day)
    {
        $count = $schedules->where('subject_id', $subject->id)->where('class_id', $class_id)->where('day', $day)->count();
        $limit = $subject->max_periods_per_day ?? $subject->max_lessons_per_day ?? 99;
        if ($count >= $limit) {
            return "Môn {$subject->name} đã đạt tối đa {$limit} tiết/ngày (Thứ {$day}).";
        }
        return false;
    }

    private function checkTeacherDailyLimit(Collection $schedules, Teacher $teacher, $day)
    {
        $count = $schedules->where('teacher_id', $teacher->id)->where('day', $day)->count();
        $limit = $teacher->max_periods_per_day ?? 5;
        if ($count >= $limit) {
            return "GV {$teacher->name} đã dạy đủ {$limit} tiết Thứ {$day}.";
        }
        return false;
    }

    private function checkTeacherWeeklyLimit(Collection $schedules, Teacher $teacher)
    {
        $count = $schedules->where('teacher_id', $teacher->id)->count();
        $limit = $teacher->quota ?? 17;
        if ($count >= $limit) {
            return "GV {$teacher->name} đã đạt định mức {$limit} tiết/tuần.";
        }
        return false;
    }

    private function checkSubjectSpreading(Collection $schedules, Subject $subject, $class_id, $day)
    {
        $class = ClassRoom::find($class_id);
        $curriculum = $class ?\App\Models\Curriculum::where('subject_id', $subject->id)->where('grade', $class->grade)->first() : null;
        $lessonsPerWeek = $curriculum ? $curriculum->lessons_per_week : ($subject->lessons_per_week ?? 99);

        if ($lessonsPerWeek < 3)
            return false;

        $todayCount = $schedules
            ->where('subject_id', $subject->id)
            ->where('class_id', $class_id)
            ->where('day', $day)
            ->count();

        if ($todayCount > 0) {
            $totalAssigned = $schedules
                ->where('subject_id', $subject->id)
                ->where('class_id', $class_id)
                ->count();

            $remaining = $lessonsPerWeek - $totalAssigned - 1;

            if ($remaining > 0 && $todayCount >= 1) {
                return "⚠️ Gợi ý: Môn {$subject->name} nên dàn đều ra các ngày. Thứ {$day} đã có {$todayCount} tiết.";
            }
        }

        return false;
    }

    private function checkRoomConflict(Collection $schedules, $room_id, $day, $period, $class_id = null, $subject_id = null)
    {
        $room = Room::find($room_id);
        if (!$room)
            return "Không tìm thấy phòng chức năng.";

        if (!$room->status) {
            return "Phòng {$room->name} đang bảo trì, không thể sử dụng.";
        }

        if ($subject_id) {
            $subject = Subject::find($subject_id);
            if ($subject && $subject->room_category_id) {
                if ($room->room_category_id !== $subject->room_category_id) {
                    $categoryName = $subject->roomCategory ? $subject->roomCategory->name : 'đã chọn';
                    return "Môn {$subject->name} yêu cầu phòng thuộc danh mục '{$categoryName}', nhưng {$room->name} không phù hợp.";
                }
            }
        }

        $roomUsageCount = $schedules
            ->where('room_id', $room_id)
            ->where('day', $day)
            ->where('period', $period)
            ->count();

        if ($roomUsageCount >= 1) {
            return "Phòng {$room->name} đã có lớp học vào Thứ {$day} - Tiết {$period}.";
        }

        if ($class_id) {
            $class = ClassRoom::find($class_id);
            if ($class && $class->student_count > $room->capacity) {
                return "Sĩ số lớp {$class->name} ({$class->student_count}) vượt quá sức chứa phòng {$room->name} ({$room->capacity}).";
            }
        }

        return false;
    }
}