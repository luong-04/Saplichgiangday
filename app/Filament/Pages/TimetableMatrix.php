<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\Room;
use App\Services\ScheduleService;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Cache;

class TimetableMatrix extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Ma trận Xếp Lịch';
    protected static ?string $title = 'Xếp Lịch Kéo - Thả';
    protected static string $view = 'filament.pages.timetable-matrix';

    public $grades = [];
    public $classes = [];
    public $subjects = [];
    public $teachers = [];
    public $rooms = [];

    public $selectedGrade = null;
    public $selectedClass = null;

    public $dragTeacherId = null;
    public $dragSubjectId = null;
    public $dragRoomId = null;

    public $matrix = [];
    public $filteredTeachers = [];
    public $filteredRooms = [];
    public $requiresRoom = false;

    // Settings
    public int $periodsPerDay = 10;
    public int $daysStart = 2;
    public int $daysEnd = 7;
    public int $lunchAfterPeriod = 5;

    public function mount()
    {
        $this->grades = ClassRoom::select('grade')->distinct()->pluck('grade', 'grade')->toArray();
        $this->subjects = Cache::remember('all_subjects', 600, fn() => Subject::all());
        $this->teachers = Teacher::withCount('schedules')->with('subjects')->get();
        $this->rooms = Room::with('subjects')->get();

        try {
            $this->periodsPerDay = Setting::periodsPerDay();
            $this->daysStart = Setting::daysStart();
            $this->daysEnd = Setting::daysEnd();
            $this->lunchAfterPeriod = Setting::lunchAfterPeriod();
        }
        catch (\Exception $e) {
        }

        $this->initMatrix();
    }

    private function initMatrix()
    {
        for ($day = $this->daysStart; $day <= $this->daysEnd; $day++) {
            for ($period = 1; $period <= $this->periodsPerDay; $period++) {
                $this->matrix[$day][$period] = null;
            }
        }
    }

    public function updatedSelectedGrade($value)
    {
        $this->classes = ClassRoom::where('grade', $value)->get();
        $this->selectedClass = null;
        $this->initMatrix();
    }

    public function updatedSelectedClass($value)
    {
        $this->initMatrix();
        if ($value) {
            // Auto-assign tiết cố định
            $service = new ScheduleService();
            $class = ClassRoom::find($value);
            if ($class) {
                $assigned = $service->autoAssignFixedPeriods($class);
                if ($assigned > 0) {
                    Notification::make()
                        ->title("Đã tự động gán {$assigned} tiết cố định")
                        ->body('Chào cờ / Sinh hoạt đã được xếp tự động.')
                        ->success()->send();
                }
            }

            // Load schedules
            $schedules = Schedule::with(['teacher', 'subject', 'room'])->where('class_id', $value)->get();
            foreach ($schedules as $sch) {
                $isFixed = $service->isFixedSubject($sch->subject);
                $this->matrix[$sch->day][$sch->period] = [
                    'id' => $sch->id,
                    'subject' => $sch->subject->name,
                    'teacher' => $sch->teacher->short_code ?? $sch->teacher->name,
                    'room' => $sch->room ? $sch->room->name : null,
                    'is_fixed' => $isFixed,
                ];
            }
        }
    }

    /**
     * Xếp lịch với validate đầy đủ.
     */
    public function assignSchedule($day, $period, $teacherId, $subjectId)
    {
        if (!$this->selectedClass)
            return;

        $service = new ScheduleService();
        $service->loadSchedules();

        $subject = Subject::find($subjectId);
        $teacher = Teacher::find($teacherId);

        if (!$subject || !$teacher) {
            Notification::make()->title('Lỗi')->body('Không tìm thấy môn học hoặc giáo viên.')->danger()->send();
            return;
        }

        $roomId = $this->dragRoomId ?: null;

        // Nếu môn cần phòng nhưng chưa chọn
        if ($subject->requiresRoom() && !$roomId) {
            Notification::make()
                ->title('Chưa chọn phòng!')
                ->body("Môn {$subject->name} là thực hành, cần chọn Phòng chức năng trước khi xếp.")
                ->danger()->send();
            return;
        }

        // Tiết đôi
        if ($subject->is_double_period) {
            $result = $service->validateDoublePeriod($teacherId, $this->selectedClass, $subjectId, $day, $period, $roomId);
            if (isset($result['error'])) {
                Notification::make()->title('Lỗi Tiết Đôi!')->body($result['error'])->danger()->send();
                return;
            }

            Schedule::create([
                'teacher_id' => $teacherId,
                'class_id' => $this->selectedClass,
                'subject_id' => $subjectId,
                'day' => $day,
                'period' => $period,
                'room_id' => $roomId,
            ]);
            Schedule::create([
                'teacher_id' => $teacherId,
                'class_id' => $this->selectedClass,
                'subject_id' => $subjectId,
                'day' => $day,
                'period' => $result['second_period'],
                'room_id' => $roomId,
            ]);

            $this->refreshAfterChange();
            Notification::make()->title('Đã xếp tiết đôi thành công')
                ->body("Tiết {$period} và tiết {$result['second_period']}")
                ->success()->send();
            return;
        }

        // Tiết đơn
        $error = $service->validate($teacherId, $this->selectedClass, $subjectId, $day, $period, $roomId);

        // Subject Spreading trả về cảnh báo (bắt đầu bằng ⚠️) → vẫn cho xếp
        if ($error && !str_starts_with($error, '⚠️')) {
            Notification::make()->title('Lỗi Xếp Lịch!')->body($error)->danger()->send();
            return;
        }

        $spreadingWarning = ($error && str_starts_with($error, '⚠️')) ? $error : null;

        Schedule::create([
            'teacher_id' => $teacherId,
            'class_id' => $this->selectedClass,
            'subject_id' => $subjectId,
            'day' => $day,
            'period' => $period,
            'room_id' => $roomId,
        ]);

        // Warnings
        $service->clearCache();
        $service->loadSchedules();
        $allSchedules = $service->loadSchedules();

        $gapWarning = $service->checkTeacherGaps($allSchedules, $teacher, $day, $period);
        $consecutiveWarning = $service->checkTeacherConsecutive($allSchedules, $teacher, $day, $period);

        $this->refreshAfterChange();

        $warning = $spreadingWarning ?: $gapWarning ?: $consecutiveWarning;
        if ($warning) {
            Notification::make()->title('Xếp lịch thành công')->body($warning)->warning()->send();
        }
        else {
            Notification::make()->title('Đã xếp lịch thành công')->success()->send();
        }
    }

    /**
     * Xóa lịch — bảo vệ tiết cố định.
     */
    public function deleteSchedule($scheduleId)
    {
        $schedule = Schedule::with('subject')->find($scheduleId);
        if (!$schedule)
            return;

        $service = new ScheduleService();
        if ($service->isFixedSubject($schedule->subject)) {
            Notification::make()
                ->title('Không thể xóa!')
                ->body("{$schedule->subject->name} là tiết cố định.")
                ->danger()->send();
            return;
        }

        $schedule->delete();
        $this->refreshAfterChange();
        Notification::make()->title('Đã xóa tiết học')->warning()->send();
    }

    private function refreshAfterChange()
    {
        Cache::forget('all_teachers');
        $this->updatedSelectedClass($this->selectedClass);
        $this->teachers = Teacher::withCount('schedules')->with('subjects')->get();
    }

    public function updatedDragSubjectId($value)
    {
        $this->dragTeacherId = null;
        $this->dragRoomId = null;
        $this->filteredRooms = [];
        $this->requiresRoom = false;

        if (!$value) {
            $this->filteredTeachers = [];
            return;
        }

        $subject = Subject::find($value);

        // Kiểm tra có cần phòng không
        if ($subject && $subject->requiresRoom()) {
            $this->requiresRoom = true;
            $this->filteredRooms = $subject->rooms()->get();
        }

        $name = mb_strtolower($subject->name);

        if (str_contains($name, 'sinh hoạt')) {
            $this->filteredTeachers = Teacher::withCount('schedules')
                ->where('homeroom_class_id', $this->selectedClass)->get();
        }
        elseif (str_contains($name, 'chào cờ')) {
            $this->filteredTeachers = Teacher::withCount('schedules')->get();
        }
        else {
            $this->filteredTeachers = Teacher::withCount('schedules')
                ->whereHas('subjects', fn($q) => $q->where('subjects.id', $value))
                ->get();
        }
    }

    public function saveTimetable()
    {
        if (!$this->selectedClass) {
            Notification::make()->title('Chưa chọn lớp!')->danger()->send();
            return;
        }
        Notification::make()->title('Lưu thành công!')->body('Thời khóa biểu đã được ghi nhận.')->success()->send();
    }
}