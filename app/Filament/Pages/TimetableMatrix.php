<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Schedule;
use App\Models\Setting;
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

    public $selectedGrade = null;
    public $selectedClass = null;

    public $dragTeacherId = null;
    public $dragSubjectId = null;

    public $matrix = [];
    public $filteredTeachers = [];

    // Cấu hình từ Settings
    public int $periodsPerDay = 10;
    public int $daysStart = 2;
    public int $daysEnd = 7;
    public int $lunchAfterPeriod = 5;

    public function mount()
    {
        $this->grades = ClassRoom::select('grade')->distinct()->pluck('grade', 'grade')->toArray();

        // Cache subjects
        $this->subjects = Cache::remember('all_subjects', 600, function () {
            return Subject::all();
        });

        // Load teachers với withCount để fix N+1 cho remaining_quota
        $this->teachers = Teacher::withCount('schedules')->with('subjects')->get();

        // Load Settings (có try-catch nếu bảng chưa tồn tại)
        try {
            $this->periodsPerDay = Setting::periodsPerDay();
            $this->daysStart = Setting::daysStart();
            $this->daysEnd = Setting::daysEnd();
            $this->lunchAfterPeriod = Setting::lunchAfterPeriod();
        }
        catch (\Exception $e) {
        // Dùng mặc định
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
            $schedules = Schedule::with(['teacher', 'subject'])->where('class_id', $value)->get();
            foreach ($schedules as $sch) {
                $service = new ScheduleService();
                $isFixed = $service->isFixedSubject($sch->subject);

                $this->matrix[$sch->day][$sch->period] = [
                    'id' => $sch->id,
                    'subject' => $sch->subject->name,
                    'teacher' => $sch->teacher->short_code ?? $sch->teacher->name,
                    'is_fixed' => $isFixed,
                ];
            }
        }
    }

    /**
     * Xếp lịch — gọi ScheduleService::validate() với ĐẦY ĐỦ ràng buộc.
     */
    public function assignSchedule($day, $period, $teacherId, $subjectId)
    {
        if (!$this->selectedClass)
            return;

        $service = new ScheduleService();
        // Load tất cả schedules 1 lần vào memory
        $service->loadSchedules();

        $subject = Subject::find($subjectId);
        $teacher = Teacher::find($teacherId);

        if (!$subject || !$teacher) {
            Notification::make()->title('Lỗi')->body('Không tìm thấy môn học hoặc giáo viên.')->danger()->send();
            return;
        }

        // Kiểm tra tiết đôi
        if ($subject->is_double_period) {
            $result = $service->validateDoublePeriod($teacherId, $this->selectedClass, $subjectId, $day, $period);

            if (isset($result['error'])) {
                Notification::make()
                    ->title('Lỗi Xếp Tiết Đôi!')
                    ->body($result['error'])
                    ->danger()
                    ->send();
                return;
            }

            // Tạo cả 2 tiết
            Schedule::create([
                'teacher_id' => $teacherId,
                'class_id' => $this->selectedClass,
                'subject_id' => $subjectId,
                'day' => $day,
                'period' => $period,
            ]);

            Schedule::create([
                'teacher_id' => $teacherId,
                'class_id' => $this->selectedClass,
                'subject_id' => $subjectId,
                'day' => $day,
                'period' => $result['second_period'],
            ]);

            $this->refreshAfterChange();
            Notification::make()
                ->title('Đã xếp tiết đôi thành công')
                ->body("Tiết {$period} và tiết {$result['second_period']}")
                ->success()
                ->send();
            return;
        }

        // Tiết đơn: gọi validate() tổng thể
        $error = $service->validate($teacherId, $this->selectedClass, $subjectId, $day, $period);

        if ($error) {
            Notification::make()
                ->title('Lỗi Xếp Lịch!')
                ->body($error)
                ->danger()
                ->send();
            return;
        }

        // Lưu vào Database
        Schedule::create([
            'teacher_id' => $teacherId,
            'class_id' => $this->selectedClass,
            'subject_id' => $subjectId,
            'day' => $day,
            'period' => $period,
        ]);

        // Kiểm tra cảnh báo tiết trống / liên tiếp (không block, chỉ warning)
        $service->clearCache();
        $service->loadSchedules();
        $allSchedules = $service->loadSchedules();

        $gapWarning = $service->checkTeacherGaps($allSchedules, $teacher, $day, $period);
        $consecutiveWarning = $service->checkTeacherConsecutive($allSchedules, $teacher, $day, $period);

        $this->refreshAfterChange();

        if ($gapWarning) {
            Notification::make()
                ->title('Xếp lịch thành công')
                ->body($gapWarning)
                ->warning()
                ->send();
        }
        elseif ($consecutiveWarning) {
            Notification::make()
                ->title('Xếp lịch thành công')
                ->body($consecutiveWarning)
                ->warning()
                ->send();
        }
        else {
            Notification::make()
                ->title('Đã xếp lịch thành công')
                ->success()
                ->send();
        }
    }

    /**
     * Xóa lịch — có bảo vệ tiết cố định.
     */
    public function deleteSchedule($scheduleId)
    {
        $schedule = Schedule::with('subject')->find($scheduleId);
        if (!$schedule)
            return;

        // Chặn xóa tiết cố định
        $service = new ScheduleService();
        if ($service->isFixedSubject($schedule->subject)) {
            Notification::make()
                ->title('Không thể xóa!')
                ->body("Tiết {$schedule->subject->name} là tiết cố định, không được xóa.")
                ->danger()
                ->send();
            return;
        }

        $schedule->delete();
        $this->refreshAfterChange();

        Notification::make()
            ->title('Đã xóa tiết học')
            ->warning()
            ->send();
    }

    /**
     * Refresh data sau khi thêm/xóa.
     */
    private function refreshAfterChange()
    {
        Cache::forget('all_teachers');
        $this->updatedSelectedClass($this->selectedClass);
        $this->teachers = Teacher::withCount('schedules')->with('subjects')->get();
    }

    /**
     * Lọc giáo viên khi chọn môn.
     */
    public function updatedDragSubjectId($value)
    {
        $this->dragTeacherId = null;
        if (!$value) {
            $this->filteredTeachers = [];
            return;
        }

        $subject = Subject::find($value);
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
                ->whereHas('subjects', function ($q) use ($value) {
                $q->where('subjects.id', $value);
            })->get();
        }
    }

    public function saveTimetable()
    {
        if (!$this->selectedClass) {
            Notification::make()->title('Chưa chọn lớp!')->danger()->send();
            return;
        }

        Notification::make()
            ->title('Lưu thành công!')
            ->body('Thời khóa biểu đã được ghi nhận.')
            ->success()
            ->send();
    }
}