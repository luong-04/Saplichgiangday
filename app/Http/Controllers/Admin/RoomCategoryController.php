<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomCategory;

class RoomCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = RoomCategory::withCount('rooms');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->paginate(15);
        return view('admin.room-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.room-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:room_categories',
            'description' => 'nullable|string',
        ]);

        RoomCategory::create($validated);

        return redirect()->route('admin.room-categories.index')->with('success', 'Thêm nhóm phòng thành công.');
    }

    public function edit(RoomCategory $roomCategory)
    {
        return view('admin.room-categories.edit', compact('roomCategory'));
    }

    public function update(Request $request, RoomCategory $roomCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:room_categories,name,' . $roomCategory->id,
            'description' => 'nullable|string',
        ]);

        $roomCategory->update($validated);

        return redirect()->route('admin.room-categories.index')->with('success', 'Cập nhật nhóm phòng thành công.');
    }

    public function destroy(RoomCategory $roomCategory)
    {
        if ($roomCategory->rooms()->count() > 0) {
            return back()->with('error', 'Không thể xóa nhóm phòng đang có phòng thuộc nhóm.');
        }

        if ($roomCategory->subjects()->count() > 0) {
            return back()->with('error', 'Không thể xóa nhóm phòng đang được yêu cầu bởi môn học.');
        }

        $roomCategory->delete();
        return redirect()->route('admin.room-categories.index')->with('success', 'Xóa nhóm phòng thành công.');
    }
}
