<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FixedPeriod;

class FixedPeriodController extends Controller
{
    public function index(Request $request)
    {
        $query = FixedPeriod::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('subject_name', 'like', "%{$search}%");
        }

        if ($request->has('shift') && $request->shift !== '') {
            $query->where('shift', $request->shift);
        }

        $fixedPeriods = $query->orderBy('day')->orderBy('period')->paginate(15);
        return view('admin.fixed-periods.index', compact('fixedPeriods'));
    }

    public function create()
    {
        return view('admin.fixed-periods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_name' => 'required|string|max:255',
            'day' => 'required|integer|between:2,7',
            'period' => 'required|integer|between:1,10',
            'shift' => 'required|in:morning,afternoon',
            'auto_assign_homeroom' => 'nullable|boolean',
        ]);

        $exists = FixedPeriod::where('day', $validated['day'])
            ->where('period', $validated['period'])
            ->where('shift', $validated['shift'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Đã tồn tại Tiết cố định tại thời gian này.')->withInput();
        }

        FixedPeriod::create([
            'subject_name' => $validated['subject_name'],
            'day' => $validated['day'],
            'period' => $validated['period'],
            'shift' => $validated['shift'],
            'auto_assign_homeroom' => $request->boolean('auto_assign_homeroom'),
        ]);

        return redirect()->route('admin.fixed-periods.index')->with('success', 'Thêm tiết cố định thành công.');
    }

    public function edit(FixedPeriod $fixedPeriod)
    {
        return view('admin.fixed-periods.edit', compact('fixedPeriod'));
    }

    public function update(Request $request, FixedPeriod $fixedPeriod)
    {
        $validated = $request->validate([
            'subject_name' => 'required|string|max:255',
            'day' => 'required|integer|between:2,7',
            'period' => 'required|integer|between:1,10',
            'shift' => 'required|in:morning,afternoon',
            'auto_assign_homeroom' => 'nullable|boolean',
        ]);

        if ($fixedPeriod->day != $validated['day'] || $fixedPeriod->period != $validated['period'] || $fixedPeriod->shift != $validated['shift']) {
            $exists = FixedPeriod::where('day', $validated['day'])
                ->where('period', $validated['period'])
                ->where('shift', $validated['shift'])
                ->exists();

            if ($exists) {
                return back()->with('error', 'Đã tồn tại Tiết cố định tại thời gian này.')->withInput();
            }
        }

        $fixedPeriod->update([
            'subject_name' => $validated['subject_name'],
            'day' => $validated['day'],
            'period' => $validated['period'],
            'shift' => $validated['shift'],
            'auto_assign_homeroom' => $request->boolean('auto_assign_homeroom'),
        ]);

        return redirect()->route('admin.fixed-periods.index')->with('success', 'Cập nhật tiết cố định thành công.');
    }

    public function destroy(FixedPeriod $fixedPeriod)
    {
        $fixedPeriod->delete();
        return redirect()->route('admin.fixed-periods.index')->with('success', 'Xóa tiết cố định thành công.');
    }
}
