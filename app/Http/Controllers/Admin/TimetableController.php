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
use App\Models\TeacherAssignment;
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
        $selectedClassId = $request->get('class_id');

        // 1. Lấy danh sách Lớp học để chọn
        $classesQuery = ClassRoom::query()->orderBy('grade')->orderBy('name');
        if ($gradeFilter)
            $classesQuery->where('grade', $gradeFilter);
        if ($shiftFilter)
            $classesQuery->where('shift', $shiftFilter);
        $classes = $classesQuery->get();

        // 2. Xác định lớp đang được chọn (mặc định lớp đầu tiên)
        $selectedClass = null;
        if ($selectedClassId) {
            $selectedClass = $classes->firstWhere('id', $selectedClassId);
        }
        if (!$selectedClass && $classes->isNotEmpty()) {
            $selectedClass = $classes->first();
            $selectedClassId = $selectedClass->id;
        }

        // 3. Lấy dữ liệu matrix cho lớp được chọn
        $matrixData = [];
        $schedules = collect();
        if ($selectedClass) {
            $schedules = Schedule::with(['subject', 'teacher', 'room'])
                ->where('class_id', $selectedClassId)
                ->get();

            foreach ($schedules as $sc) {
                $matrixData[$sc->day][$sc->period] = $sc;
            }
        }

        // 4. LOGIC KHO THẺ (CARD POOL) - Chỉ cho lớp được chọn
        $cardPool = [];
        if ($selectedClass) {
            $assignments = TeacherAssignment::with(['subject', 'teacher'])
                ->where('class_id', $selectedClassId)
                ->get();

            foreach ($assignments as $as) {
                $totalNeeded = $as->subject->lessons_per_week;
                $alreadyScheduled = $schedules->where('subject_id', $as->subject_id)
                    ->where('teacher_id', $as->teacher_id)
                    ->count();

                $remaining = $totalNeeded - $alreadyScheduled;
                if ($remaining > 0) {
                    $cardPool[] = [
                        'subject_id' => $as->subject_id,
                        'subject_name' => $as->subject->name,
                        'subject_type' => $as->subject->type,
                        'teacher_id' => $as->teacher_id,
                        'teacher_name' => $as->teacher->name,
                        'count' => $remaining
                    ];
                }
            }
        }

        $teachers = Teacher::all();
        $subjects = Subject::all();
        $rooms = Room::where('status', true)->get();

        return view('admin.timetable.matrix', compact(
            'classes', 'selectedClass', 'matrixData', 'cardPool',
            'daysStart', 'daysEnd', 'periodsPerDay',
            'teachers', 'subjects', 'rooms',
            'gradeFilter', 'shiftFilter'
        ));
    }

    /**
     * Xuất Ma trận TKB (Toàn khối/Toàn trường) để In ấn chuyên nghiệp.
     */
    public function matrixExport(Request $request)
    {
        $gradeFilter = $request->grade;
        $shiftFilter = $request->shift;

        $classesQuery = ClassRoom::query();
        if ($gradeFilter)
            $classesQuery->where('grade', $gradeFilter);
        if ($shiftFilter)
            $classesQuery->where('shift', $shiftFilter);
        $classes = $classesQuery->orderBy('name')->get();

        $schedules = Schedule::with(['subject', 'teacher', 'room', 'classRoom'])->get();
        $daysStart = Setting::daysStart();
        $daysEnd = Setting::daysEnd();
        $periodsPerDay = Setting::periodsPerDay();

        $matrixData = [];
        foreach ($schedules as $sc) {
            $matrixData[$sc->class_id][$sc->day][$sc->period][] = $sc;
        }

        return view('admin.timetable.matrix-export', compact(
            'classes', 'matrixData',
            'daysStart', 'daysEnd', 'periodsPerDay',
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
        // 1. Chỉ thực hiện gán các tiết cố định theo cấu hình
        $classes = ClassRoom::all();
        $totalFixed = 0;
        foreach ($classes as $class) {
            $totalFixed += $this->scheduleService->autoAssignFixedPeriods($class);
        }

        // 2. Không chạy AutoScheduleService->run() nữa 
        // Hoặc nếu chạy, chỉ chạy một logic rất nhẹ để kiểm tra lỗi.

        return redirect()->route('admin.timetable.matrix')
            ->with('success', "Đã tự động điền $totalFixed tiết cố định vào TKB.");
    }

    public function updateSchedule(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $newDay = $request->day;
        $newPeriod = $request->period;

        // Sử dụng ScheduleService để validate toàn diện
        $error = $this->scheduleService->validate(
            $schedule->teacher_id,
            $schedule->class_id,
            $schedule->subject_id,
            $newDay,
            $newPeriod,
            $schedule->room_id,
            $schedule->id
        );

        if ($error) {
            return response()->json([
                'success' => false,
                'message' => $error
            ]);
        }

        $schedule->update([
            'day' => $newDay,
            'period' => $newPeriod,
            'is_manual' => true,
        ]);
        return response()->json(['success' => true]);
    }

    /**
     * Hoán đổi vị trí 2 tiết học (kéo tiết A sang ô tiết B → B về vị trí cũ của A).
     */
    public function swapSchedule(Request $request)
    {
        $data = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'target_day' => 'required|integer',
            'target_period' => 'required|integer',
            'target_class_id' => 'required|integer',
        ]);

        $draggedSchedule = Schedule::findOrFail($data['schedule_id']);
        $oldDay = $draggedSchedule->day;
        $oldPeriod = $draggedSchedule->period;
        $oldClassId = $draggedSchedule->class_id;

        // Tìm tiết học tại ô đích (nếu có)
        $targetSchedule = Schedule::where('class_id', $data['target_class_id'])
            ->where('day', $data['target_day'])
            ->where('period', $data['target_period'])
            ->first();

        // Kiểm tra xung đột cho tiết kéo bằng ScheduleService
        $draggedError = $this->scheduleService->validate(
            $draggedSchedule->teacher_id,
            $data['target_class_id'],
            $draggedSchedule->subject_id,
            $data['target_day'],
            $data['target_period'],
            $draggedSchedule->room_id,
            $draggedSchedule->id
        );
        if ($draggedError) {
            return response()->json(['success' => false, 'message' => "Tiết kéo: " . $draggedError]);
        }

        // Nếu ô đích có tiết → kiểm tra xung đột GV cho tiết đích khi di chuyển về ô cũ
        if ($targetSchedule) {
            $targetError = $this->scheduleService->validate(
                $targetSchedule->teacher_id,
                $oldClassId,
                $targetSchedule->subject_id,
                $oldDay,
                $oldPeriod,
                $targetSchedule->room_id,
                $targetSchedule->id
            );
            if ($targetError) {
                return response()->json(['success' => false, 'message' => "Tiết đích: " . $targetError]);
            }

            // Hoán đổi: tiết đích → vị trí cũ
            $targetSchedule->update([
                'day' => $oldDay,
                'period' => $oldPeriod,
                'class_id' => $oldClassId,
                'is_manual' => true,
            ]);
        }

        // Tiết kéo → vị trí đích
        $draggedSchedule->update([
            'day' => $data['target_day'],
            'period' => $data['target_period'],
            'class_id' => $data['target_class_id'],
            'is_manual' => true,
        ]);

        return response()->json(['success' => true, 'swapped' => $targetSchedule !== null]);
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

        // Validate bằng ScheduleService
        $error = $this->scheduleService->validate(
            $data['teacher_id'],
            $data['class_id'],
            $data['subject_id'],
            $data['day'],
            $data['period'],
            $data['room_id'] ?? null
        );

        if ($error) {
            return response()->json(['success' => false, 'message' => $error]);
        }

        // Xóa tiết cũ tại ô này (nếu có - thực tế validate() class_id/day/period conflict sẽ bắt được, 
        // nhưng nếu muốn ghi đè thì phải xử lý khác. Ở đây ta tuân thủ validate() chặn trùng)

        $data['is_manual'] = true;
        Schedule::create($data);
        return response()->json(['success' => true]);
    }

    public function deleteSchedule($id)
    {
        Schedule::destroy($id);
        return response()->json(['success' => true]);
    }
}
