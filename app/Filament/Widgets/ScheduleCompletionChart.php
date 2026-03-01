<?php

namespace App\Filament\Widgets;

use App\Models\ClassRoom;
use App\Models\Schedule;
use Filament\Widgets\ChartWidget;

class ScheduleCompletionChart extends ChartWidget
{
    protected static ?string $heading = 'Tiến Độ Xếp Lịch Theo Khối';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $maxHeight = '320px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $grades = ['10', '11', '12'];
        $labels = [];
        $completedData = [];
        $remainingData = [];

        foreach ($grades as $grade) {
            $classIds = ClassRoom::where('grade', $grade)->pluck('id');
            $classCount = $classIds->count();
            $totalSlots = $classCount * 60;
            $filledSlots = Schedule::whereIn('class_id', $classIds)->count();

            $labels[] = "Khối {$grade}";
            $completedData[] = $filledSlots;
            $remainingData[] = max(0, $totalSlots - $filledSlots);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Đã xếp lịch',
                    'data' => $completedData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => '#3b82f6',
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                    'borderSkipped' => false,
                ],
                [
                    'label' => 'Còn trống',
                    'data' => $remainingData,
                    'backgroundColor' => 'rgba(186, 230, 253, 0.7)',
                    'borderColor' => '#bae6fd',
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                    'borderSkipped' => false,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 20,
                        'font' => ['size' => 13, 'weight' => '600'],
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'ticks' => ['font' => ['size' => 14, 'weight' => '700']],
                ],
                'y' => [
                    'grid' => ['color' => 'rgba(0,0,0,0.04)'],
                    'ticks' => ['font' => ['size' => 12]],
                    'beginAtZero' => true,
                ],
            ],
        ];
    }
}
