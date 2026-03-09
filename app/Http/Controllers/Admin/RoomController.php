<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomCategory;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with('category');

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
}
