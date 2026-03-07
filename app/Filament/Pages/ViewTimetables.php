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

    public function loadData()
    {
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