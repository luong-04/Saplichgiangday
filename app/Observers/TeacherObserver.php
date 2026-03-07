<?php

namespace App\Observers;

use App\Models\Teacher;
use App\Models\Schedule;
use App\Models\Subject;
use App\Services\ScheduleService;
use App\Models\ClassRoom;

class TeacherObserver
{
    /**
     * Khi Teacher được cập nhật — kiểm tra homeroom_class_id thay đổi.
     */
    public function updating(Teacher $teacher): void
    {
        $oldClassId = $teacher->getOriginal('homeroom_class_id');
        $newClassId = $teacher->homeroom_class_id;

        // Nếu homeroom_class_id không thay đổi → bỏ qua
        if ($oldClassId == $newClassId) {
            return;
        }

        // === BỎ GVCN khỏi lớp cũ → xóa tiết cố định của lớp cũ ===
        if ($oldClassId) {
            $this->deleteFixedSchedules($oldClassId, $teacher->id);
        }
    }

    /**
     * Sau khi Teacher được cập nhật — gán tiết cố định cho lớp mới.
     */
    public function updated(Teacher $teacher): void
    {
        $oldClassId = $teacher->getOriginal('homeroom_class_id');
        $newClassId = $teacher->homeroom_class_id;

        if ($oldClassId == $newClassId) {
            return;
        }

        // === GÁN GVCN cho lớp mới → tự động tạo tiết cố định ===
        if ($newClassId) {
            $class = ClassRoom::find($newClassId);
            if ($class) {
                $service = new ScheduleService();
                $service->autoAssignFixedPeriods($class);
            }
        }
    }

    /**
     * Xóa tất cả tiết cố định (Chào cờ, Sinh hoạt) của lớp.
     */
    private function deleteFixedSchedules(int $classId, int $teacherId): void
    {
        $fixedSubjectIds = Subject::where(function ($q) {
            $q->where('name', 'like', '%Chào cờ%')
                ->orWhere('name', 'like', '%Sinh hoạt%');
        })->pluck('id')->toArray();

        if (empty($fixedSubjectIds))
            return;

        Schedule::where('class_id', $classId)
            ->whereIn('subject_id', $fixedSubjectIds)
            ->delete();
    }
}
