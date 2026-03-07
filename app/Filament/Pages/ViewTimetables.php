<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ClassRoom;
use App\Models\Schedule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimetableExport;
use Illuminate\Support\Facades\Cache;

class ViewTimetables extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationLabel = 'Thời Khóa Biểu';
    protected static ?string $title = 'Hệ Thống Thời Khóa Biểu';
    protected static string $view = 'filament.pages.view-timetables';

    public $selectedGrade = '10';
    public $timetables = [];

    public function mount()
    {
        $this->loadData();
    }
    public function updatedSelectedGrade()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // Lấy lớp kèm giáo viên chủ nhiệm
        $classes = ClassRoom::with('teacher')
            ->where('grade', $this->selectedGrade)
            ->orderBy('name')
            ->get();

        if ($classes->isEmpty()) {
            $this->timetables = [];
            return;
        }

        $classIds = $classes->pluck('id');

        // 1 query duy nhất lấy TẤT CẢ schedules cho tất cả lớp trong khối
        // (thay vì N query — 1 per class)
        $allSchedules = Schedule::with(['teacher', 'subject'])
            ->whereIn('class_id', $classIds)
            ->get()
            ->groupBy('class_id');

        $this->timetables = [];

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

    public function exportExcel($classId)
    {
        $tkbData = collect($this->timetables)->firstWhere('id', $classId);
        if ($tkbData) {
            $fileName = 'TKB_Lop_' . $tkbData['name'] . '.xlsx';
            return Excel::download(new TimetableExport($tkbData), $fileName);
        }
    }
}