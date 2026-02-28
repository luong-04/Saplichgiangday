<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Schedule;

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
        // Kiểm tra đầu vào
        $request->validate([
            'lookup_code' => 'required|string|max:255',
        ]);

        $code = $request->lookup_code;
        $schedules = collect(); 
        $targetName = '';
        $type = '';

        // 1. Kiểm tra xem mã này thuộc về Giáo viên hay Lớp học [cite: 28]
        $teacher = Teacher::where('lookup_code', $code)->first();
        $classRoom = ClassRoom::where('lookup_code', $code)->first();

        if ($teacher) {
            // Lấy lịch của Giáo viên
            $schedules = Schedule::with(['classRoom', 'subject'])
                ->where('teacher_id', $teacher->id)
                ->orderBy('day')
                ->orderBy('period')
                ->get();
            $targetName = $teacher->name;
            $type = 'Giáo viên';
        } elseif ($classRoom) {
            // Lấy lịch của Lớp học
            $schedules = Schedule::with(['teacher', 'subject'])
                ->where('class_id', $classRoom->id)
                ->orderBy('day')
                ->orderBy('period')
                ->get();
            $targetName = $classRoom->name;
            $type = 'Lớp';
        } else {
            // Kịch bản TC-06: Nhập mã không tồn tại [cite: 123]
            return back()->with('error', 'Mã không hợp lệ hoặc không tìm thấy dữ liệu.');
        }

        // Trả kết quả về giao diện
        return view('search', compact('schedules', 'targetName', 'code', 'type'));
    }
}