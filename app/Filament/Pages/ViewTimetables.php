<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ClassRoom;
use App\Models\Schedule;

class ViewTimetables extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    protected static ?string $navigationLabel = 'Thời Khóa Biểu';
    protected static ?string $title = 'Hệ Thống Thời Khóa Biểu';
    protected static string $view = 'filament.pages.view-timetables';

    public $selectedGrade = '10';
    public $timetables = [];

    public function mount() { $this->loadData(); }
    public function updatedSelectedGrade() { $this->loadData(); }

    public function loadData()
    {
        // Lấy lớp kèm theo thông tin giáo viên chủ nhiệm
        $classes = ClassRoom::with('teacher')->where('grade', $this->selectedGrade)->get();
        $this->timetables = [];

        foreach ($classes as $class) {
            $schedules = Schedule::with(['teacher', 'subject'])->where('class_id', $class->id)->get();
            $data = [];
            foreach ($schedules as $s) {
                $data[$s->day][$s->period] = [
                    'sub' => $s->subject->name,
                    'tea' => $s->teacher->short_code ?? $s->teacher->name
                ];
            }

            $this->timetables[] = [
                'id' => $class->id,
                'name' => $class->name,
                'gvcn' => $class->teacher ? $class->teacher->name : 'Chưa có',
                'data' => $data
            ];
        }
    }
}