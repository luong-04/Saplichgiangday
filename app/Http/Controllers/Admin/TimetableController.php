<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassRoom;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Room;
use App\Models\Setting;
use App\Services\ScheduleService;
use App\Services\AutoScheduleService;

class TimetableController extends Controller
{
    protected $scheduleService;
    protected $autoScheduleService;

    public function __construct(ScheduleService $scheduleService, AutoScheduleService $autoScheduleService)
    {
        $this->scheduleService = $scheduleService;
        $this->autoScheduleService = $autoScheduleService;
    }

    public function matrix(Request $request)
    {
        $daysStart = Setting::daysStart();
        $daysEnd = Setting::daysEnd();
        $periodsPerDay = Setting::periodsPerDay();

        $gradeFilter = $request->get('grade');
        $shiftFilter = $request->get('shift');

        // Lấy danh sách Lớp học
        $classesQuery = ClassRoom::query()->orderBy('grade')->orderBy('name');
        if ($gradeFilter) {
            $classesQuery->where('grade', $gradeFilter);
        }
        if ($shiftFilter) {
            $classesQuery->where('shift', $shiftFilter);
        }
        $classes = $classesQuery->get();

        // Lấy danh sách Thời khóa biểu
        $schedulesQuery = Schedule::with(['subject', 'teacher', 'room']);
        if ($gradeFilter || $shiftFilter) {
            $schedulesQuery->whereIn('class_id', $classes->pluck('id'));
        }
        $schedules = $schedulesQuery->get();

        // Group TKB theo [class_id][day][period]
        $matrixData = [];
        foreach ($schedules as $sc) {
            $matrixData[$sc->class_id][$sc->day][$sc->period] = $sc;
        }

        // Lấy danh sách GV, Môn, Phòng để dùng trong Modal (nếu làm modal gán tay)
        $teachers = Teacher::with('subjects')->get();
        $subjects = Subject::all();
        $rooms = Room::where('status', true)->get();

        return view('admin.timetable.matrix', compact(
            'classes', 'matrixData',
            'daysStart', 'daysEnd', 'periodsPerDay',
            'teachers', 'subjects', 'rooms',
            'gradeFilter', 'shiftFilter'
        ));
    }

    public function viewTimetables(Request $request)
    {
        $daysStart = Setting::daysStart();
        $daysEnd = Setting::daysEnd();
        $periodsPerDay = Setting::periodsPerDay();

        $type = $request->get('type', 'class'); // 'class' or 'teacher'
        $id = $request->get('id');

        $classes = ClassRoom::orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();

        $schedules = collect();
        $selectedName = null;
        $timetableData = [];

        if ($type === 'class' && $id) {
            $class = ClassRoom::find($id);
            if ($class) {
                $selectedName = "Lớp " . $class->name;
                $schedules = Schedule::with(['subject', 'teacher', 'room'])
                    ->where('class_id', $id)
                    ->get();
            }
        }
        elseif ($type === 'teacher' && $id) {
            $teacher = Teacher::find($id);
            if ($teacher) {
                $selectedName = "Giáo viên " . $teacher->name;
                $schedules = Schedule::with(['subject', 'classRoom', 'room'])
                    ->where('teacher_id', $id)
                    ->get();
            }
        }

        // Group TKB theo [day][period]
        foreach ($schedules as $sc) {
            $timetableData[$sc->day][$sc->period][] = $sc; // Mảng vì GV có thể (lỗi) bị trùng tiết, list ra để dễ thấy
        }

        return view('admin.timetable.view', compact(
            'classes', 'teachers', 'type', 'id',
            'daysStart', 'daysEnd', 'periodsPerDay',
            'timetableData', 'selectedName'
        ));
    }

    public function autoSchedule(Request $request)
    {
        // 1. Chạy AutoAssignFixedPeriods cho tất cả các lớp trước
        $classes = ClassRoom::all();
        foreach ($classes as $class) {
            $this->scheduleService->autoAssignFixedPeriods($class);
        }

        // 2. Chạy AutoScheduleService để xếp các môn còn lại
        $clearExisting = $request->boolean('clear', true); // Mặc định là xóa trắng TKB hiện tại (trừ các tiết cố định)
        $stats = $this->autoScheduleService->run($clearExisting);

        $msg = "Xếp tự động hoàn tất. Thành công: {$stats['success']} tiết. Thất bại: {$stats['failed']} tiết.";

        if ($stats['failed'] > 0) {
            session()->flash('warning', "Không thể xếp hết 100% tiết học, vui lòng xếp tay thêm.");
        }

        return redirect()->route('admin.timetable.matrix')->with('success', $msg);
    }

    public function updateSchedule(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->update([
            'day' => $request->day,
            'period' => $request->period,
        ]);
        return response()->json(['success' => true]);
    }

    public function assignSchedule(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required',
            'day' => 'required',
            'period' => 'required',
            'subject_id' => 'required',
            'teacher_id' => 'required',
            'room_id' => 'nullable',
        ]);

        // Xóa tiết cũ tại ô này (nếu có)
        Schedule::where('class_id', $data['class_id'])
            ->where('day', $data['day'])
            ->where('period', $data['period'])
            ->delete();

        Schedule::create($data);
        return response()->json(['success' => true]);
    }

    public function deleteSchedule($id)
    {
        Schedule::destroy($id);
        return response()->json(['success' => true]);
    }
}
