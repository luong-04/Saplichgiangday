<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Schedule;
use App\Services\ScheduleService;
use Filament\Notifications\Notification;

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
    
    // Biến lưu Giáo viên và Môn học đang được chọn để kéo thả
    public $dragTeacherId = null;
    public $dragSubjectId = null;

    public $matrix = [];

    public function mount()
    {
        $this->grades = ClassRoom::select('grade')->distinct()->pluck('grade', 'grade')->toArray();
        $this->subjects = Subject::all();
        $this->teachers = Teacher::all();
        $this->initMatrix();
    }

    private function initMatrix()
    {
        for ($day = 2; $day <= 7; $day++) {
            for ($period = 1; $period <= 10; $period++) {
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
                $this->matrix[$sch->day][$sch->period] = [
                    'id' => $sch->id,
                    'subject' => $sch->subject->name,
                    'teacher' => $sch->teacher->short_code ?? $sch->teacher->name,
                ];
            }
        }
    }

    // Hàm Xếp lịch (Nhận data từ giao diện khi người dùng thả chuột)
    public function assignSchedule($day, $period, $teacherId, $subjectId)
    {
        if (!$this->selectedClass) return;

        $service = new ScheduleService();
        $conflict = $service->checkConflict($teacherId, $this->selectedClass, $day, $period);

        if ($conflict) {
            // Hiển thị thông báo lỗi màu đỏ (Filament Notification)
            Notification::make()
                ->title('Lỗi Xếp Lịch!')
                ->body($conflict)
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

        // Cập nhật lại giao diện và thông báo thành công
        $this->updatedSelectedClass($this->selectedClass);
        Notification::make()
            ->title('Đã xếp lịch thành công')
            ->success()
            ->send();
    }

    // Hàm Xóa lịch trực tiếp trên Ma trận
    public function deleteSchedule($scheduleId)
    {
        Schedule::find($scheduleId)?->delete();
        $this->updatedSelectedClass($this->selectedClass);
        Notification::make()
            ->title('Đã xóa tiết học')
            ->warning()
            ->send();
    }
    // Thêm biến chứa danh sách giáo viên đã lọc
    public $filteredTeachers = [];

    // Hàm này tự động chạy khi bạn chọn một Môn học ở cột trái
    public function updatedDragSubjectId($value) {
        $this->dragTeacherId = null; // Reset giáo viên khi đổi môn
        if (!$value) { $this->filteredTeachers = []; return; }

        $subject = Subject::find($value);
        $name = mb_strtolower($subject->name);

        // 1. Sinh hoạt lớp: Chỉ hiện GV chủ nhiệm lớp này
        if (str_contains($name, 'sinh hoạt')) {
            $this->filteredTeachers = Teacher::where('homeroom_class_id', $this->selectedClass)->get();
        } 
        // 2. Chào cờ: Hiện tất cả giáo viên
        else if (str_contains($name, 'chào cờ')) {
            $this->filteredTeachers = Teacher::all();
        } 
        // 3. Môn bình thường: Tìm giáo viên dạy môn đó (quan hệ nhiều-nhiều)
        else {
            $this->filteredTeachers = Teacher::whereHas('subjects', function($q) use ($value) {
                $q->where('subjects.id', $value);
            })->get();
        }
    }
    public function saveTimetable()
    {
    if (!$this->selectedClass) {
        \Filament\Notifications\Notification::make()->title('Chưa chọn lớp!')->danger()->send();
        return;
    }

    // Chỉ gửi thông báo, KHÔNG dùng redirect() để ở lại trang tạo cái mới
    \Filament\Notifications\Notification::make()
        ->title('Lưu thành công!')
        ->body('Thời khóa biểu đã được ghi nhận. Bạn có thể tiếp tục xếp lịch cho lớp khác.')
        ->success()
        ->send();
    }
}