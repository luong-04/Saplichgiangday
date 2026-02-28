<?php

namespace App\Services;

use App\Models\Schedule;

class ScheduleService
{
    /**
     * Kiểm tra trùng lịch (CheckConflict)
     * Trả về string (thông báo lỗi) nếu có trùng, trả về false nếu hợp lệ.
     */
    public function checkConflict($teacher_id, $class_id, $day, $period, $ignore_schedule_id = null)
    {
        // 1. Kiểm tra: Giáo viên đã có lịch ở lớp khác vào cùng Thứ & Tiết chưa?
        $teacherConflict = Schedule::where('teacher_id', $teacher_id)
            ->where('day', $day)
            ->where('period', $period);
            
        if ($ignore_schedule_id) {
            $teacherConflict->where('id', '!=', $ignore_schedule_id); // Bỏ qua bản ghi đang sửa
        }
        
        if ($teacherConflict->exists()) {
            return "Giáo viên này đã có lịch dạy vào Thứ {$day} - Tiết {$period}.";
        }

        // 2. Kiểm tra: Lớp học đã có môn khác vào cùng Thứ & Tiết chưa?
        $classConflict = Schedule::where('class_id', $class_id)
            ->where('day', $day)
            ->where('period', $period);
            
        if ($ignore_schedule_id) {
            $classConflict->where('id', '!=', $ignore_schedule_id);
        }

        if ($classConflict->exists()) {
            return "Lớp này đã có môn học vào Thứ {$day} - Tiết {$period}.";
        }

        return false; // Không trùng lịch
    }
}