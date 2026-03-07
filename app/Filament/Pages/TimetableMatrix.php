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

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('autoSchedule')
            ->label('Tự động lấp đầy')
            ->color('success')
            ->icon('heroicon-o-sparkles')
            ->requiresConfirmation()
            ->modalHeading('Xếp lịch tự động')
            ->modalDescription('Hệ thống sẽ tự động ghép các tiết học còn thiếu vào khoảng trống dựa trên ràng buộc. Việc này có thể xóa kết quả xếp cũ. Bạn có chắc chắn?')
            ->action(function () {
            $service = new \App\Services\AutoScheduleService(new \App\Services\ScheduleService());
            $stats = $service->run(true);

            if ($stats['failed'] > 0) {
                Notification::make()
                    ->title("Thành công {$stats['success']} tiết. Bỏ sót {$stats['failed']} tiết.")
                    ->body("Một số tiết không thể xếp do thiếu slot hoặc đụng giờ giáo viên/phòng.")
                    ->warning()
                    ->send();
            }
            else {
                Notification::make()
                    ->title('Xếp tự động hoàn tất!')
                    ->body("Đã xếp thành công toàn bộ {$stats['success']} tiết.")
                    ->success()
                    ->send();
            }

            if ($this->selectedClass) {
                $this->updatedSelectedClass($this->selectedClass);
            }
            $this->refreshAfterChange();
        }),
        ];
    }

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
        $this->rooms = Room::all();

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
        $enforce = true;
        try {
            $enforce = Setting::enforceRoomAssignment();
        }
        catch (\Exception $e) {
        }

        // Nếu môn cần phòng nhưng chưa chọn
        if ($subject->requiresRoom() && !$roomId && $enforce) {
            Notification::make()
                ->title('Chưa chọn phòng!')
                ->body("Môn {$subject->name} là thực hành, cần chọn Phòng chức năng trước khi xếp.")
                ->danger()->send();
            return;
        }

        // Tiết dài (consecutive_periods)
        $consecutive = $subject->consecutive_periods ?? 1;
        if ($consecutive > 1) {
            $result = $service->validateMultiPeriod($teacherId, $this->selectedClass, $subjectId, $day, $period, $roomId);
            if (isset($result['error'])) {
                Notification::make()->title('Lỗi Tiết Dài!')->body($result['error'])->danger()->send();
                return;
            }

            foreach ($result['periods'] as $p) {
                Schedule::create([
                    'teacher_id' => $teacherId,
                    'class_id' => $this->selectedClass,
                    'subject_id' => $subjectId,
                    'day' => $day,
                    'period' => $p,
                    'room_id' => $roomId,
                ]);
            }

            $this->refreshAfterChange();
            $periodStr = implode(', ', $result['periods']);
            Notification::make()->title("Đã xếp $consecutive tiết thành công")
                ->body("Các tiết: {$periodStr}")
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
            if ($subject->room_category_id) {
                $this->filteredRooms = Room::where('room_category_id', $subject->room_category_id)
                    ->where('status', true)->get();
            }
            else {
                $this->filteredRooms = Room::where('status', true)->get();
            }
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