<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Curriculum;
use App\Models\Subject;

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        $query = Curriculum::with('subject');

        if ($request->has('grade') && $request->grade !== '') {
            $query->where('grade', $request->grade);
        }

        if ($request->has('subject_id') && $request->subject_id !== '') {
            $query->where('subject_id', $request->subject_id);
        }

        $curricula = $query->orderBy('grade')->paginate(15);
        $subjects = Subject::all();

        return view('admin.curricula.index', compact('curricula', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('admin.curricula.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|in:10,11,12',
            'subject_id' => 'required|exists:subjects,id',
            'lessons_per_week' => 'required|integer|min:1',
        ]);

        // Kiểm tra xem đã tồn tại phân phối chương trình này chưa
        $exists = Curriculum::where('grade', $validated['grade'])
            ->where('subject_id', $validated['subject_id'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Chương trình học này đã tồn tại trong hệ thống.')->withInput();
        }

        Curriculum::create($validated);

        return redirect()->route('admin.curricula.index')->with('success', 'Thêm chương trình học thành công.');
    }

    public function edit(Curriculum $curriculum)
    {
        $subjects = Subject::all();
        return view('admin.curricula.edit', compact('curriculum', 'subjects'));
    }

    public function update(Request $request, Curriculum $curriculum)
    {
        $validated = $request->validate([
            'grade' => 'required|integer|in:10,11,12',
            'subject_id' => 'required|exists:subjects,id',
            'lessons_per_week' => 'required|integer|min:1',
        ]);

        // Cập nhật môn hoặc khối thì check trùng
        if ($curriculum->grade != $validated['grade'] || $curriculum->subject_id != $validated['subject_id']) {
            $exists = Curriculum::where('grade', $validated['grade'])
                ->where('subject_id', $validated['subject_id'])
                ->exists();

            if ($exists) {
                return back()->with('error', 'Chương trình học này đã tồn tại trong hệ thống.')->withInput();
            }
        }

        $curriculum->update($validated);

        return redirect()->route('admin.curricula.index')->with('success', 'Cập nhật chương trình học thành công.');
    }

    public function destroy(Curriculum $curriculum)
    {
        $curriculum->delete();
        return redirect()->route('admin.curricula.index')->with('success', 'Xóa chương trình học thành công.');
    }
}
