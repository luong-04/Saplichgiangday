<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Schedule;
use App\Models\Room;
use App\Models\Setting;

class AutoScheduleService
{
    protected ScheduleService $validator;

    public function __construct(ScheduleService $validator)
    {
        $this->validator = $validator;
    }

    public function run($clearExisting = true): array
    {
        if ($clearExisting) {
            $this->clearNonFixedSchedules();
        }

        $classes = ClassRoom::all();
        $subjects = Subject::all();

        // Lấy danh sách môn học và sắp xếp theo độ ưu tiên
        // Ưu tiên 1: Cần phòng chức năng (room_category_id != null)
        // Ưu tiên 2: Tiết liền (consecutive_periods > 1)
        // Ưu tiên 3: Còn lại
        $prioritizedSubjects = $subjects->sortByDesc(function ($subject) {
            $score = 0;
            if ($subject->room_category_id)
                $score += 100;
            if ($subject->consecutive_periods > 1)
                $score += 50;
            return $score;
        });

        $this->validator->clearCache();

        $stats = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];

        $days = [2, 3, 4, 5, 6, 7];
        $periods = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]; // 5 sáng, 5 chiều

        foreach ($prioritizedSubjects as $subject) {

            // Bỏ qua các môn cố định (đã được AutoAssignFixedPeriods lo)
            if ($this->validator->isFixedSubject($subject)) {
                continue;
            }

            foreach ($classes as $class) {
                // Xác định số tiết mục tiêu (ưu tiên cấu hình Khung chương trình theo Khối)
                $curriculum = \App\Models\Curriculum::where('subject_id', $subject->id)
                    ->where('grade', $class->grade)
                    ->first();
                $targetLessons = $curriculum ? $curriculum->lessons_per_week : $subject->lessons_per_week;
                $currentLessons = Schedule::where('subject_id', $subject->id)
                    ->where('class_id', $class->id)
                    ->count();

                $needed = $targetLessons - $currentLessons;

                while ($needed > 0) {
                    $scheduled = false;

                    // Lấy giáo viên được phân công đích danh thông qua TeacherAssignment
                    $assignedTeachers = Teacher::whereHas('assignments', function ($q) use ($subject, $class) {
                        $q->where('subject_id', $subject->id)
                            ->where('class_id', $class->id);
                    })->get();

                    // Nếu không có ai được phân công đích danh, lấy danh sách giáo viên có dạy môn này
                    if ($assignedTeachers->isEmpty()) {
                        $teachers = Teacher::whereHas('subjects', function ($q) use ($subject) {
                            $q->where('subjects.id', $subject->id);
                        })->get();
                    }
                    else {
                        $teachers = $assignedTeachers;
                    }

                    // Sắp xếp giáo viên theo số tiết còn lại giảm dần (để ưu tiên người còn rảnh)
                    $teachers = $teachers->sortByDesc(function ($t) {
                        $assigned = Schedule::where('teacher_id', $t->id)->count();
                        return $t->quota - $assigned;
                    });

                    // Tìm một slot khả dĩ
                    foreach ($teachers as $teacher) {

                        // Nếu giáo viên có quy định loại phòng dạy, và môn học yêu cầu phòng -> phải khớp
                        if ($subject->room_category_id && $teacher->room_category_ids) {
                            if (!in_array($subject->room_category_id, $teacher->room_category_ids)) {
                                continue; // Bỏ qua giáo viên này
                            }
                        }

                        $rooms = collect([null]);
                        if ($subject->room_category_id) {
                            $rooms = Room::where('room_category_id', $subject->room_category_id)
                                ->where('status', true)
                                ->where('capacity', '>=', $class->student_count)
                                ->get();
                        }

                        foreach ($days as $day) {
                            foreach ($periods as $period) {
                                foreach ($rooms as $room) {
                                    $roomId = $room ? $room->id : null;

                                    if ($subject->consecutive_periods > 1) {
                                        $result = $this->validator->validateMultiPeriod($teacher->id, $class->id, $subject->id, $day, $period, $roomId);
                                        if (isset($result['ok'])) {
                                            foreach ($result['periods'] as $p) {
                                                Schedule::create([
                                                    'teacher_id' => $teacher->id,
                                                    'class_id' => $class->id,
                                                    'subject_id' => $subject->id,
                                                    'room_id' => $roomId,
                                                    'day' => $day,
                                                    'period' => $p,
                                                ]);
                                            }
                                            $this->validator->clearCache();
                                            $needed -= count($result['periods']);
                                            $stats['success']++;
                                            $scheduled = true;
                                            break 4; // Break out of all loops to schedule the next lesson limit constraint
                                        }
                                    }
                                    else {
                                        $error = $this->validator->validate($teacher->id, $class->id, $subject->id, $day, $period, $roomId);
                                        if (!$error) {
                                            // Xếp thành công
                                            Schedule::create([
                                                'teacher_id' => $teacher->id,
                                                'class_id' => $class->id,
                                                'subject_id' => $subject->id,
                                                'room_id' => $roomId,
                                                'day' => $day,
                                                'period' => $period,
                                            ]);
                                            $this->validator->clearCache();
                                            $needed--;
                                            $stats['success']++;
                                            $scheduled = true;
                                            break 4;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if (!$scheduled) {
                        // Không thể xếp được thêm cho môn này của lớp này
                        $stats['failed']++;
                        $stats['errors'][] = "Lớp {$class->name} - Môn {$subject->name}: Thiếu {$needed} tiết không thể xếp do kẹt ràng buộc.";
                        break; // Thoát vòng lặp while needed
                    }
                }
            }
        }

        return $stats;
    }

    private function clearNonFixedSchedules()
    {
        $schedules = Schedule::all();
        foreach ($schedules as $s) {
            $subject = Subject::find($s->subject_id);
            if (!$subject || !$this->validator->isFixedSubject($subject)) {
                $s->delete();
            }
        }
    }
}
