<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Schedule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TimetableExport;

class SearchController extends Controller
{
    // Hiển thị trang chủ tra cứu
    public function index()
    {
        return view('search');
    }

    // Xử lý khi người dùng nhập mã và bấm Tìm kiếm
    public function search(Request $request)
    {
        $request->validate([
            'lookup_code' => 'required|string|max:255',
        ]);

        $code = $request->lookup_code;
        $schedules = collect();
        $targetName = '';
        $type = '';
        $gvcn = '';

        $teacher = Teacher::where('lookup_code', $code)->first();
        $classRoom = ClassRoom::where('lookup_code', $code)->first();

        if ($teacher) {
            $schedules = Schedule::with(['classRoom', 'subject'])
                ->where('teacher_id', $teacher->id)
                ->orderBy('day')
                ->orderBy('period')
                ->get();
            $targetName = $teacher->name;
            $type = 'Giáo viên';
        }
        elseif ($classRoom) {
            $schedules = Schedule::with(['teacher', 'subject'])
                ->where('class_id', $classRoom->id)
                ->orderBy('day')
                ->orderBy('period')
                ->get();
            $targetName = $classRoom->name;
            $type = 'Lớp';
            // Lấy GVCN
            $gvcnTeacher = Teacher::where('homeroom_class_id', $classRoom->id)->first();
            $gvcn = $gvcnTeacher ? $gvcnTeacher->name : 'Chưa có';
        }
        else {
            return back()->with('error', 'Mã không hợp lệ hoặc không tìm thấy dữ liệu.');
        }

        // Chuyển đổi sang dạng grid [day][period]
        $grid = [];
        foreach ($schedules as $s) {
            $grid[$s->day][$s->period] = [
                'subject' => $s->subject->name,
                'extra' => $type === 'Giáo viên'
                ? ($s->classRoom->name ?? '')
                : ($s->teacher->short_code ?? $s->teacher->name ?? ''),
            ];
        }

        return view('search', compact('schedules', 'targetName', 'code', 'type', 'grid', 'gvcn'));
    }

    // Xuất Excel từ trang tra cứu
    public function exportExcel($code)
    {
        $classRoom = ClassRoom::where('lookup_code', $code)->first();

        if (!$classRoom) {
            return back()->with('error', 'Không tìm thấy lớp để xuất Excel.');
        }

        $schedules = Schedule::with(['teacher', 'subject'])->where('class_id', $classRoom->id)->get();
        $data = [];
        foreach ($schedules as $s) {
            $data[$s->day][$s->period] = [
                'sub' => $s->subject->name,
                'tea' => $s->teacher->short_code ?? $s->teacher->name,
            ];
        }

        $gvcnTeacher = Teacher::where('homeroom_class_id', $classRoom->id)->first();
        $tkb = [
            'id' => $classRoom->id,
            'name' => $classRoom->name,
            'gvcn' => $gvcnTeacher ? $gvcnTeacher->name : 'Chưa có',
            'data' => $data,
        ];

        $fileName = 'TKB_Lop_' . $classRoom->name . '.xlsx';
        return Excel::download(new TimetableExport($tkb), $fileName);
    }
}