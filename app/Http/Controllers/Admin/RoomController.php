<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomCategory;
use App\Models\Schedule;
use App\Models\Subject;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with('roomCategory');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->has('room_category_id') && $request->room_category_id !== '') {
            $query->where('room_category_id', $request->room_category_id);
        }

        $rooms = $query->paginate(15);
        $categories = RoomCategory::all();
        return view('admin.rooms.index', compact('rooms', 'categories'));
    }

    public function create()
    {
        $categories = RoomCategory::all();
        return view('admin.rooms.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms',
            'capacity' => 'required|integer|min:1',
            'room_category_id' => 'nullable|exists:room_categories,id',
        ]);

        Room::create($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Thêm phòng học thành công.');
    }

    public function edit(Room $room)
    {
        $categories = RoomCategory::all();
        return view('admin.rooms.edit', compact('room', 'categories'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name,' . $room->id,
            'capacity' => 'required|integer|min:1',
            'room_category_id' => 'nullable|exists:room_categories,id',
        ]);

        $room->update($validated);

        return redirect()->route('admin.rooms.index')->with('success', 'Cập nhật phòng học thành công.');
    }

    public function destroy(Room $room)
    {
        if ($room->schedules()->count() > 0) {
            return back()->with('error', 'Không thể xóa phòng học đã được xếp lịch.');
        }

        $room->delete();
        return redirect()->route('admin.rooms.index')->with('success', 'Xóa phòng học thành công.');
    }

    /**
     * API: Lấy danh sách phòng khả dụng cho một tiết học cụ thể.
     */
    public function availableRooms(Request $request)
    {
        $day = $request->day;
        $period = $request->period;
        $subjectId = $request->subject_id;

        $subject = Subject::findOrFail($subjectId);
        $categoryId = $subject->room_category_id;

        $query = Room::with('roomCategory')->where('status', true);

        // Lọc theo loại phòng nếu môn học yêu cầu
        if ($categoryId) {
            $query->where('room_category_id', $categoryId);
        }

        // Loại bỏ các phòng đã bị chiếm tại thời điểm này
        $occupiedRoomIds = Schedule::where('day', $day)
            ->where('period', $period)
            ->pluck('room_id')
            ->filter()
            ->toArray();

        $rooms = $query->whereNotIn('id', $occupiedRoomIds)->get();

        return response()->json($rooms->map(function ($room) {
            return [
                'id' => $room->id,
                'name' => $room->name,
                'category_name' => $room->roomCategory->name ?? 'Mặc định'
            ];
        }));
    }
}
