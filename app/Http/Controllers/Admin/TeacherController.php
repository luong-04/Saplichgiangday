<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\ClassRoom;
use App\Models\TeacherAssignment;
use App\Models\Schedule;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::with(['subjects', 'homeroomClass', 'assignments']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('short_code', 'like', "%{$search}%")
                ->orWhere('lookup_code', 'like', "%{$search}%");
        }

        $teachers = $query->paginate(15);

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $subjects = Subject::all();
        $classes = ClassRoom::all();
        $homeroomClasses = ClassRoom::doesntHave('homeroomTeacher')->get();
        return view('admin.teachers.create', compact('subjects', 'classes', 'homeroomClasses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_code' => 'nullable|string|max:255',
            'lookup_code' => 'required|string|max:255|unique:teachers',
            'quota' => 'required|integer|min:0',
            'max_periods_per_day' => 'required|integer|min:1|max:10',
            'homeroom_class_id' => 'nullable|exists:classes,id',
            'subjects' => 'required|array',
            'subjects.*' => 'exists:subjects,id',
            'teaching_shifts' => 'nullable|array',
            'assignments' => 'nullable|array',
            'assignments.*.class_id' => 'required|exists:classes,id',
            'assignments.*.subject_id' => 'required|exists:subjects,id',
        ]);

        $teacher = Teacher::create([
            'name' => $validated['name'],
            'short_code' => $validated['short_code'],
            'lookup_code' => $validated['lookup_code'],
            'quota' => $validated['quota'],
            'max_periods_per_day' => $validated['max_periods_per_day'],
            'homeroom_class_id' => $validated['homeroom_class_id'] ?? null,
            'teaching_shifts' => $validated['teaching_shifts'] ?? [],
        ]);

        if (!empty($validated['subjects'])) {
            $teacher->subjects()->sync($validated['subjects']);
        }

        // Đồng bộ Phân công giảng dạy (Logic Premium)
        if ($request->has('assignments')) {
            foreach ($request->assignments as $assignment) {
                if (!empty($assignment['class_id']) && !empty($assignment['subject_id'])) {
                    TeacherAssignment::create([
                        'teacher_id' => $teacher->id,
                        'class_id' => $assignment['class_id'],
                        'subject_id' => $assignment['subject_id']
                    ]);
                }
            }
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Thêm giáo viên thành công.');
    }

    public function edit(Teacher $teacher)
    {
        $subjects = Subject::all();
        $classes = ClassRoom::all();
        // Lấy tất cả lớp chưa có chủ nhiệm HOẶC lớp đang do giáo viên này chủ nhiệm
        $homeroomClasses = ClassRoom::whereDoesntHave('homeroomTeacher')
            ->orWhere('id', $teacher->homeroom_class_id)
            ->get();

        $teacher->load('assignments');

        return view('admin.teachers.edit', compact('teacher', 'subjects', 'classes', 'homeroomClasses'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_code' => 'nullable|string|max:255',
            'lookup_code' => 'required|string|max:255|unique:teachers,lookup_code,' . $teacher->id,
            'quota' => 'required|integer|min:0',
            'max_periods_per_day' => 'required|integer|min:1|max:10',
            'homeroom_class_id' => 'nullable|exists:classes,id',
            'subjects' => 'required|array',
            'subjects.*' => 'exists:subjects,id',
            'teaching_shifts' => 'nullable|array',
            'assignments' => 'nullable|array',
            'assignments.*.class_id' => 'required|exists:classes,id',
            'assignments.*.subject_id' => 'required|exists:subjects,id',
        ]);

        $teacher->update([
            'name' => $validated['name'],
            'short_code' => $validated['short_code'],
            'lookup_code' => $validated['lookup_code'],
            'quota' => $validated['quota'],
            'max_periods_per_day' => $validated['max_periods_per_day'],
            'homeroom_class_id' => $validated['homeroom_class_id'] ?? null,
            'teaching_shifts' => $validated['teaching_shifts'] ?? [],
        ]);

        if (isset($validated['subjects'])) {
            $teacher->subjects()->sync($validated['subjects']);
        }
        else {
            $teacher->subjects()->sync([]);
        }

        // Đồng bộ Phân công giảng dạy (Logic Premium)
        $teacher->assignments()->delete();
        if ($request->has('assignments')) {
            foreach ($request->assignments as $assignment) {
                if (!empty($assignment['class_id']) && !empty($assignment['subject_id'])) {
                    TeacherAssignment::create([
                        'teacher_id' => $teacher->id,
                        'class_id' => $assignment['class_id'],
                        'subject_id' => $assignment['subject_id']
                    ]);
                }
            }
        }

        return redirect()->route('admin.teachers.index')->with('success', 'Cập nhật giáo viên thành công.');
    }

    public function destroy(Teacher $teacher)
    {
        // Kiểm tra xem giáo viên có đang được phân công thời khóa biểu không
        if ($teacher->schedules()->count() > 0) {
            return back()->with('error', 'Không thể xóa giáo viên đang có lịch phân công.');
        }

        $teacher->subjects()->detach();
        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Xóa giáo viên thành công.');
    }

    /**
     * API: Lấy danh sách các tiết bận của giáo viên (Đã dạy + Đã khóa bận).
     */
    public function busySlots(Teacher $teacher)
    {
        // 1. Tiết đã có lịch dạy
        $scheduledSlots = $teacher->schedules()->select('day', 'period')->get()->map(function ($sc) {
            return "{$sc->day}-{$sc->period}";
        })->toArray();

        // 2. Tiết đã đánh dấu bận trong profile (availability)
        $unavailableSlots = [];
        if (!empty($teacher->availability)) {
            foreach ($teacher->availability as $day => $periods) {
                if (is_array($periods)) {
                    foreach ($periods as $p) {
                        $unavailableSlots[] = "{$day}-{$p}";
                    }
                }
            }
        }

        return response()->json([
            'busy' => array_unique(array_merge($scheduledSlots, $unavailableSlots))
        ]);
    }
}
