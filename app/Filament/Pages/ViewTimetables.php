<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\ClassRoom;
use App\Models\Schedule;

class ViewTimetables extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    protected static ?string $navigationLabel = 'Thời Khóa Biểu';
    protected static ?string $title = 'Thời Khóa Biểu Toàn Trường';
    protected static string $view = 'filament.pages.view-timetables';

    public $selectedGrade = null;
    public $timetables = [];

    public function mount()
    {
        $this->selectedGrade = '10'; // Mặc định hiện khối 10
        $this->loadTimetables();
    }

    public function updatedSelectedGrade()
    {
        $this->loadTimetables();
    }

    public function loadTimetables()
    {
        $classes = ClassRoom::where('grade', $this->selectedGrade)->get();
        $this->timetables = [];

        foreach ($classes as $class) {
            $schedules = Schedule::with(['teacher', 'subject'])->where('class_id', $class->id)->get();
            $matrix = [];
            foreach ($schedules as $sch) {
                $matrix[$sch->day][$sch->period] = [
                    'sub' => $sch->subject->name,
                    'tea' => $sch->teacher->short_code ?? $sch->teacher->name
                ];
            }
            $this->timetables[] = ['name' => $class->name, 'data' => $matrix];
        }
    }
}