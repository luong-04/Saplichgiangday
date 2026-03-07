<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\Setting;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimetableExport;

class ViewTimetables extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationLabel = 'Thời Khóa Biểu';
    protected static ?string $title = 'Hệ Thống Thời Khóa Biểu';
    protected static string $view = 'filament.pages.view-timetables';

    public $viewMode = 'class';
    public $selectedGrade = '10';
    public $timetables = [];

    // Cấu hình từ Settings
    public int $periodsPerDay = 10;
    public int $daysStart = 2;
    public int $daysEnd = 7;
    public int $lunchAfterPeriod = 5;

    public function mount()
    {
        try {
            $this->periodsPerDay = Setting::periodsPerDay();
            $this->daysStart = Setting::daysStart();
            $this->daysEnd = Setting::daysEnd();
            $this->lunchAfterPeriod = Setting::lunchAfterPeriod();
        }
        catch (\Exception $e) {
        }

        $this->loadData();
    }

    public function updatedSelectedGrade()
    {
        $this->loadData();
    }

    public function updatedViewMode()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->timetables = [];

        if ($this->viewMode === 'room') {
            $rooms = \App\Models\Room::orderBy('name')->get();
            if ($rooms->isEmpty())
                return;

            $allSchedules = Schedule::with(['teacher', 'subject', 'classRoom'])
                ->whereNotNull('room_id')
                ->get()
                ->groupBy('room_id');

            foreach ($rooms as $room) {
                $data = [];
                $schedules = $allSchedules->get($room->id, collect());

                foreach ($schedules as $s) {
                    $className = $s->classRoom ? $s->classRoom->name : '';
                    $data[$s->day][$s->period] = [
                        'sub' => $s->subject->name . ' - ' . $className,
                        'tea' => $s->teacher->short_code ?? $s->teacher->name,
                    ];
                }

                if (!empty($data)) {
                    $this->timetables[] = [
                        'id' => $room->id,
                        'name' => $room->name,
                        'gvcn' => 'Sức chứa: ' . $room->capacity,
                        'data' => $data,
                    ];
                }
            }
        }
        else {
            $classes = ClassRoom::with('teacher')
                ->where('grade', $this->selectedGrade)
                ->orderBy('name')
                ->get();

            if ($classes->isEmpty()) {
                return;
            }

            $classIds = $classes->pluck('id');

            // 1 query duy nhất lấy TẤT CẢ schedules cho tất cả lớp trong khối
            $allSchedules = Schedule::with(['teacher', 'subject'])
                ->whereIn('class_id', $classIds)
                ->get()
                ->groupBy('class_id');

            foreach ($classes as $class) {
                $data = [];
                $schedules = $allSchedules->get($class->id, collect());

                foreach ($schedules as $s) {
                    $data[$s->day][$s->period] = [
                        'sub' => $s->subject->name,
                        'tea' => $s->teacher->short_code ?? $s->teacher->name,
                    ];
                }

                $this->timetables[] = [
                    'id' => $class->id,
                    'name' => $class->name,
                    'gvcn' => $class->teacher ? $class->teacher->name : 'Chưa có',
                    'data' => $data,
                ];
            }
        }
    }

    public function exportExcel($classId)
    {
        $tkbData = collect($this->timetables)->firstWhere('id', $classId);
        if ($tkbData) {
            $fileName = 'TKB_Lop_' . $tkbData['name'] . '.xlsx';
            return Excel::download(new TimetableExport($tkbData), $fileName);
        }
    }
}