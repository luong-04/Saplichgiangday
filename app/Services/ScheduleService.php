<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;

class ScheduleService
{
    /**
     * Validate toàn bộ ràng buộc trước khi xếp lịch.
     * Trả về string (thông báo lỗi) nếu vi phạm, trả về false nếu hợp lệ.
     */
    public function validate($teacher_id, $class_id, $subject_id, $day, $period, $ignore_schedule_id = null)
    {
        // 1. Kiểm tra trùng lịch cơ bản
        $conflict = $this->checkConflict($teacher_id, $class_id, $day, $period, $ignore_schedule_id);
        if ($conflict)
            return $conflict;

        // 2. Lấy thông tin Subject và Teacher
        $subject = Subject::find($subject_id);
        $teacher = Teacher::find($teacher_id);

        if (!$subject || !$teacher) {
            return "Không tìm thấy thông tin môn học hoặc giáo viên.";
        }

        // 3. Kiểm tra giáo viên có rảnh ở thứ/tiết này không
        $availability = $this->checkTeacherAvailability($teacher, $day, $period);
        if ($availability)
            return $availability;

        // 4. Kiểm tra số tiết tối đa môn học / tuần
        $weeklyLimit = $this->checkSubjectWeeklyLimit($subject, $class_id, $ignore_schedule_id);
        if ($weeklyLimit)
            return $weeklyLimit;

        // 5. Kiểm tra số tiết tối đa môn học / ngày
        $dailySubjectLimit = $this->checkSubjectDailyLimit($subject, $class_id, $day, $ignore_schedule_id);
        if ($dailySubjectLimit)
            return $dailySubjectLimit;

        // 6. Kiểm tra số tiết tối đa giáo viên / ngày
        $dailyTeacherLimit = $this->checkTeacherDailyLimit($teacher, $day, $ignore_schedule_id);
        if ($dailyTeacherLimit)
            return $dailyTeacherLimit;

        // 7. Kiểm tra định mức tiết/tuần của giáo viên
        $weeklyTeacherLimit = $this->checkTeacherWeeklyLimit($teacher, $ignore_schedule_id);
        if ($weeklyTeacherLimit)
            return $weeklyTeacherLimit;

        return false; // Tất cả đều hợp lệ
    }

    /**
     * Kiểm tra trùng lịch (CheckConflict)
     */
    public function checkConflict($teacher_id, $class_id, $day, $period, $ignore_schedule_id = null)
    {
        // 1. Giáo viên đã có lịch ở lớp khác vào cùng Thứ & Tiết?
        $teacherConflict = Schedule::where('teacher_id', $teacher_id)
            ->where('day', $day)
            ->where('period', $period);

        if ($ignore_schedule_id) {
            $teacherConflict->where('id', '!=', $ignore_schedule_id);
        }

        if ($teacherConflict->exists()) {
            return "Giáo viên này đã có lịch dạy vào Thứ {$day} - Tiết {$period}.";
        }

        // 2. Lớp học đã có môn khác vào cùng Thứ & Tiết?
        $classConflict = Schedule::where('class_id', $class_id)
            ->where('day', $day)
            ->where('period', $period);

        if ($ignore_schedule_id) {
            $classConflict->where('id', '!=', $ignore_schedule_id);
        }

        if ($classConflict->exists()) {
            return "Lớp này đã có môn học vào Thứ {$day} - Tiết {$period}.";
        }

        return false;
    }

    /**
     * Kiểm tra giáo viên có rảnh ở thứ/tiết này không.
     */
    public function checkTeacherAvailability(Teacher $teacher, $day, $period)
    {
        if (!$teacher->isAvailable((int)$day, (int)$period)) {
            return "Giáo viên {$teacher->name} không rảnh vào Thứ {$day} - Tiết {$period}.";
        }
        return false;
    }

    /**
     * Kiểm tra số tiết tối đa của môn học trong tuần cho 1 lớp.
     */
    public function checkSubjectWeeklyLimit(Subject $subject, $class_id, $ignore_schedule_id = null)
    {
        $query = Schedule::where('subject_id', $subject->id)
            ->where('class_id', $class_id);

        if ($ignore_schedule_id) {
            $query->where('id', '!=', $ignore_schedule_id);
        }

        $currentCount = $query->count();
        $limit = $subject->lessons_per_week ?? 99;

        if ($currentCount >= $limit) {
            return "Môn {$subject->name} đã đủ {$limit} tiết/tuần cho lớp này. Không thể xếp thêm.";
        }

        return false;
    }

    /**
     * Kiểm tra số tiết tối đa của môn học trong ngày cho 1 lớp.
     */
    public function checkSubjectDailyLimit(Subject $subject, $class_id, $day, $ignore_schedule_id = null)
    {
        $query = Schedule::where('subject_id', $subject->id)
            ->where('class_id', $class_id)
            ->where('day', $day);

        if ($ignore_schedule_id) {
            $query->where('id', '!=', $ignore_schedule_id);
        }

        $currentCount = $query->count();
        $limit = $subject->max_lessons_per_day ?? 99;

        if ($currentCount >= $limit) {
            return "Môn {$subject->name} đã đạt tối đa {$limit} tiết/ngày (Thứ {$day}) cho lớp này.";
        }

        return false;
    }

    /**
     * Kiểm tra số tiết tối đa giáo viên dạy trong ngày.
     */
    public function checkTeacherDailyLimit(Teacher $teacher, $day, $ignore_schedule_id = null)
    {
        $query = Schedule::where('teacher_id', $teacher->id)
            ->where('day', $day);

        if ($ignore_schedule_id) {
            $query->where('id', '!=', $ignore_schedule_id);
        }

        $currentCount = $query->count();
        $limit = $teacher->max_periods_per_day ?? 5;

        if ($currentCount >= $limit) {
            return "GV {$teacher->name} đã dạy đủ {$limit} tiết trong Thứ {$day}. Không thể xếp thêm.";
        }

        return false;
    }

    /**
     * Kiểm tra định mức tiết/tuần của giáo viên.
     */
    public function checkTeacherWeeklyLimit(Teacher $teacher, $ignore_schedule_id = null)
    {
        $query = Schedule::where('teacher_id', $teacher->id);

        if ($ignore_schedule_id) {
            $query->where('id', '!=', $ignore_schedule_id);
        }

        $currentCount = $query->count();
        $limit = $teacher->quota ?? 17;

        if ($currentCount >= $limit) {
            return "GV {$teacher->name} đã đạt định mức {$limit} tiết/tuần. Không thể xếp thêm.";
        }

        return false;
    }
}