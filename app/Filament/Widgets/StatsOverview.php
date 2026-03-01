<?php

namespace App\Filament\Widgets;

use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalTeachers = Teacher::count();
        $totalClasses = ClassRoom::count();
        $totalSchedules = Schedule::count();
        $totalSubjects = Subject::count();

        // Tính tỷ lệ hoàn thành
        $totalSlots = $totalClasses * 60; // 10 tiết x 6 ngày
        $completionRate = $totalSlots > 0 ? round(($totalSchedules / $totalSlots) * 100, 1) : 0;

        // Đếm lớp theo khối
        $k10 = ClassRoom::where('grade', '10')->count();
        $k11 = ClassRoom::where('grade', '11')->count();
        $k12 = ClassRoom::where('grade', '12')->count();

        return [
            Stat::make('Giáo viên', $totalTeachers)
            ->description('Tổng cán bộ giảng dạy')
            ->descriptionIcon('heroicon-m-users')
            ->chart([4, 6, 8, 7, 12, 10, $totalTeachers])
            ->color('info'),

            Stat::make('Lớp học', $totalClasses)
            ->description("K10: {$k10} · K11: {$k11} · K12: {$k12}")
            ->descriptionIcon('heroicon-m-academic-cap')
            ->chart([3, 5, 7, 6, 9, 8, $totalClasses])
            ->color('success'),

            Stat::make('Môn học', $totalSubjects)
            ->description('Các bộ môn giảng dạy')
            ->descriptionIcon('heroicon-m-book-open')
            ->chart([2, 4, 3, 5, $totalSubjects])
            ->color('warning'),

            Stat::make('Tiết đã xếp', $totalSchedules)
            ->description("Hoàn thành: {$completionRate}%")
            ->descriptionIcon('heroicon-m-calendar-days')
            ->chart([7, 3, 10, 5, 15, 8, 20, $totalSchedules])
            ->color('primary'),
        ];
    }
}