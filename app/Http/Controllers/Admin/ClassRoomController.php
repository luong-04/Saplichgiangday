<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassRoom;
use App\Models\Room;

class ClassRoomController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassRoom::with(['homeroomTeacher', 'defaultRoom']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('grade') && $request->grade !== '') {
            $query->where('grade', $request->grade);
        }

        $classes = $query->orderBy('grade')->orderBy('name')->paginate(15);

        return view('admin.class-rooms.index', compact('classes'));
    }

    public function create()
    {
        $rooms = Room::all();
        return view('admin.class-rooms.create', compact('rooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:classes',
            'grade' => 'required|integer|in:10,11,12',
            'shift' => 'required|in:morning,afternoon',
            'default_room_id' => 'nullable|exists:rooms,id',
        ]);

        ClassRoom::create($validated);

        return redirect()->route('admin.class-rooms.index')->with('success', 'Thêm lớp học thành công.');
    }

    public function edit(ClassRoom $classRoom)
    {
        $rooms = Room::all();
        return view('admin.class-rooms.edit', compact('classRoom', 'rooms'));
    }

    public function update(Request $request, ClassRoom $classRoom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:classes,name,' . $classRoom->id,
            'grade' => 'required|integer|in:10,11,12',
            'shift' => 'required|in:morning,afternoon',
            'default_room_id' => 'nullable|exists:rooms,id',
        ]);

        $classRoom->update($validated);

        return redirect()->route('admin.class-rooms.index')->with('success', 'Cập nhật lớp học thành công.');
    }

    public function destroy(ClassRoom $classRoom)
    {
        if ($classRoom->schedules()->count() > 0) {
            return back()->with('error', 'Không thể xóa lớp học đã được xếp lịch.');
        }

        // Bỏ liên kết của giáo viên nếu lớp này bị xóa mà vẫn có người phân công lớp này (đã dùng json assigned_classes nên không xoá pivot tự động được, kệ nó, khi lưu teacher sẽ ghi đè).

        $classRoom->delete();
        return redirect()->route('admin.class-rooms.index')->with('success', 'Xóa lớp học thành công.');
    }
}
