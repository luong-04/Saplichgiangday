<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TimetableExport implements FromView, ShouldAutoSize, WithStyles
{
    public $tkb;

    public function __construct($tkb)
    {
        $this->tkb = $tkb;
    }

    public function view(): View
    {
        // Gọi đến 1 giao diện HTML đơn giản để render ra Excel
        return view('exports.timetable', [
            'tkb' => $this->tkb
        ]);
    }

    // Trang trí Excel: In đậm, cỡ chữ
    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'size' => 14]],
            2    => ['font' => ['italic' => true, 'bold' => true]],
            4    => ['font' => ['bold' => true]],
        ];
    }
}