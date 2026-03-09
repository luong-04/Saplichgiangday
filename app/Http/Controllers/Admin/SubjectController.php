<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\RoomCategory;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with('roomCategory');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $subjects = $query->paginate(15);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        $roomCategories = RoomCategory::all();
        return view('admin.subjects.create', compact('roomCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects',
            'type' => 'required|in:1,2',
            'is_fixed' => 'nullable|boolean',
            'lessons_per_week' => 'required|integer|min:1',
            'max_lessons_per_day' => 'required|integer|min:1|max:5',
            'consecutive_periods' => 'required|integer|min:1|max:5',
            'max_periods_per_day' => 'required|integer|min:1|max:10',
            'room_category_id' => 'nullable|exists:room_categories,id',
        ]);

        Subject::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'is_fixed' => $request->boolean('is_fixed'),
            'lessons_per_week' => $validated['lessons_per_week'],
            'max_lessons_per_day' => $validated['max_lessons_per_day'],
            'consecutive_periods' => $validated['consecutive_periods'],
            'max_periods_per_day' => $validated['max_periods_per_day'],
            'room_category_id' => $validated['room_category_id'] ?? null,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Thêm môn học thành công.');
    }

    public function edit(Subject $subject)
    {
        $roomCategories = RoomCategory::all();
        return view('admin.subjects.edit', compact('subject', 'roomCategories'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:subjects,name,' . $subject->id,
            'type' => 'required|in:1,2',
            'is_fixed' => 'nullable|boolean',
            'lessons_per_week' => 'required|integer|min:1',
            'max_lessons_per_day' => 'required|integer|min:1|max:5',
            'consecutive_periods' => 'required|integer|min:1|max:5',
            'max_periods_per_day' => 'required|integer|min:1|max:10',
            'room_category_id' => 'nullable|exists:room_categories,id',
        ]);

        $subject->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'is_fixed' => $request->boolean('is_fixed'),
            'lessons_per_week' => $validated['lessons_per_week'],
            'max_lessons_per_day' => $validated['max_lessons_per_day'],
            'consecutive_periods' => $validated['consecutive_periods'],
            'max_periods_per_day' => $validated['max_periods_per_day'],
            'room_category_id' => $validated['room_category_id'] ?? null,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Cập nhật môn học thành công.');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->schedules()->count() > 0) {
            return back()->with('error', 'Không thể xóa môn học đã được xếp lịch.');
        }

        $subject->delete();
        return redirect()->route('admin.subjects.index')->with('success', 'Xóa môn học thành công.');
    }
}
