<?php

namespace App\Filament\Widgets;

use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Schedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Widget này sẽ hiển thị các con số tổng quan của trường
    protected function getStats(): array
    {
        return [
            Stat::make('Tổng số Giáo viên', Teacher::count())
                ->description('Cán bộ, nhân viên nhà trường')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
                
            Stat::make('Số Lớp học', ClassRoom::count())
                ->description('Khối 10, 11 và 12')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),
                
            Stat::make('Tiết dạy đã xếp', Schedule::count())
                ->description('Tiến độ hoàn thành TKB hiện tại')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->chart([7, 3, 10, 5, 15, 8, 20]) // Biểu đồ minh họa sự tăng trưởng
                ->color('primary'),
        ];
    }
}