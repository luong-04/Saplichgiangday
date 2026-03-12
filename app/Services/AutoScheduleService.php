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
                $score += 1000;
            if ($subject->consecutive_periods > 1)
                $score += 500;
            return $score;
        });

        $this->validator->clearCache();
        $cachedSchedules = $this->validator->loadSchedules();

        $teachers = Teacher::with('subjects')->get();
        $rooms = Room::where('status', true)->get();

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

                // PERFORMANCE: Use cachedSchedules instead of DB query
                $currentLessons = $cachedSchedules->where('subject_id', $subject->id)
                    ->where('class_id', $class->id)
                    ->count();

                $needed = $targetLessons - $currentLessons;

                while ($needed > 0) {
                    $scheduled = false;

                    // Lấy giáo viên được phân công đích danh
                    $classTeachers = $teachers->filter(function ($t) use ($subject, $class) {
                        return $t->subjects->contains('id', $subject->id) &&
                        is_array($t->assigned_classes) &&
                        in_array((string)$class->id, $t->assigned_classes);
                    });

                    if ($classTeachers->isEmpty()) {
                        $stats['failed']++;
                        $stats['errors'][] = "Lớp {$class->name} - Môn {$subject->name}: Thiếu {$needed} tiết. Chưa có Giáo viên nào được phân công dạy.";
                        break;
                    }

                    // Sắp xếp giáo viên theo số tiết còn lại giảm dần
                    $classTeachers = $classTeachers->sortByDesc(function ($t) use ($cachedSchedules) {
                        $assigned = $cachedSchedules->where('teacher_id', $t->id)->count();
                        return ($t->quota ?? 17) - $assigned;
                    });

                    // Tính số tiết liền lớn nhất có thể xếp trong lần này
                    $consecutiveToTry = ($subject->consecutive_periods > 1 && $needed > 1) ? min($needed, $subject->consecutive_periods) : 1;

                    // SLICING LOGIC: If we can't fit consecutive, we try smaller until 1
                    for ($c = $consecutiveToTry; $c >= 1; $c--) {
                        foreach ($classTeachers as $teacher) {
                            if ($subject->room_category_id && $teacher->room_category_ids) {
                                if (!in_array($subject->room_category_id, $teacher->room_category_ids)) {
                                    continue;
                                }
                            }

                            $availableRooms = collect([null]);
                            if ($subject->room_category_id) {
                                $availableRooms = $rooms->filter(function ($r) use ($subject, $class) {
                                    return $r->room_category_id == $subject->room_category_id &&
                                    $r->capacity >= $class->student_count;
                                });
                            }
                            else if ($class->default_room_id) {
                                $defaultRoom = $rooms->firstWhere('id', $class->default_room_id);
                                if ($defaultRoom) {
                                    $availableRooms = collect([$defaultRoom]);
                                }
                            }

                            foreach ($days as $day) {
                                foreach ($periods as $period) {
                                    foreach ($availableRooms as $room) {
                                        $roomId = $room ? $room->id : null;

                                        if ($c > 1) {
                                            $result = $this->validator->validateMultiPeriod($teacher->id, $class->id, $subject->id, $day, $period, $roomId, null, $c);
                                            if (isset($result['ok'])) {
                                                foreach ($result['periods'] as $p) {
                                                    $newSchedule = Schedule::create([
                                                        'teacher_id' => $teacher->id,
                                                        'class_id' => $class->id,
                                                        'subject_id' => $subject->id,
                                                        'room_id' => $roomId,
                                                        'day' => $day,
                                                        'period' => $p,
                                                        'is_manual' => false,
                                                    ]);
                                                    $cachedSchedules->push($newSchedule);
                                                }
                                                $needed -= count($result['periods']);
                                                $stats['success']++;
                                                $scheduled = true;
                                                break 4;
                                            }
                                        }
                                        else {
                                            $error = $this->validator->validate($teacher->id, $class->id, $subject->id, $day, $period, $roomId);
                                            if (!$error) {
                                                $newSchedule = Schedule::create([
                                                    'teacher_id' => $teacher->id,
                                                    'class_id' => $class->id,
                                                    'subject_id' => $subject->id,
                                                    'room_id' => $roomId,
                                                    'day' => $day,
                                                    'period' => $period,
                                                    'is_manual' => false,
                                                ]);
                                                $cachedSchedules->push($newSchedule);
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
                        if ($scheduled)
                            break; // If we scheduled something (even a slice), go back to recalculate needed and try the next lesson
                    }

                    if (!$scheduled) {
                        $stats['failed']++;
                        $stats['errors'][] = "Lớp {$class->name} - Môn {$subject->name}: Thiếu {$needed} tiết không thể xếp do kẹt ràng buộc.";
                        break;
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
            // Protect: 1. Fixed Subjects, 2. Manual Schedules
            if ($subject && ($this->validator->isFixedSubject($subject) || $s->is_manual)) {
                continue;
            }
            $s->delete();
        }
    }
}
