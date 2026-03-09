@extends('admin.layouts.app')

@section('title', 'Ma trận Xếp Lịch TKB')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Ma trận Xếp Lịch TKB</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Quan sát & điểu chỉnh cục diện Thời khóa biểu toàn trường</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.timetable.matrix') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Tải lại
        </a>
        <form action="{{ route('admin.timetable.auto') }}" method="POST" onsubmit="return confirm('Hành động này sẽ XÓA TOÀN BỘ TKB hiện tại (trừ Tiết cố định) và tự động xếp lại. Bạn có chắc chắn không?');">
            @csrf
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-bold flex items-center gap-2 shadow-lg shadow-emerald-200 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                Xếp tự động (Auto)
            </button>
        </form>
    </div>
</div>

<div class="content-card mb-6">
    <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <form action="{{ route('admin.timetable.matrix') }}" method="GET" class="w-full flex-1 flex flex-wrap gap-3">
            <select name="grade" class="px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white text-sm font-medium" onchange="this.form.submit()">
                <option value="">Tất cả Khối</option>
                <option value="10" {{ $gradeFilter == '10' ? 'selected' : '' }}>Khối 10</option>
                <option value="11" {{ $gradeFilter == '11' ? 'selected' : '' }}>Khối 11</option>
                <option value="12" {{ $gradeFilter == '12' ? 'selected' : '' }}>Khối 12</option>
            </select>
            <select name="shift" class="px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white text-sm font-medium" onchange="this.form.submit()">
                <option value="">Tất cả Ca học</option>
                <option value="morning" {{ $shiftFilter == 'morning' ? 'selected' : '' }}>Ca Sáng</option>
                <option value="afternoon" {{ $shiftFilter == 'afternoon' ? 'selected' : '' }}>Ca Chiều</option>
            </select>
            @if($gradeFilter || $shiftFilter)
                <a href="{{ route('admin.timetable.matrix') }}" class="px-4 py-2 text-slate-500 hover:text-slate-800 text-sm font-medium flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Xóa lọc
                </a>
            @endif
        </form>
        <div class="text-sm border border-slate-200 bg-slate-50 rounded-lg px-3 py-2 font-medium text-slate-600 flex gap-4">
            <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-slate-100 border border-slate-300"></div> Trống</span>
            <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-blue-100 border border-blue-300"></div> Lý thuyết</span>
            <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-purple-100 border border-purple-300"></div> Thực hành</span>
            <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-emerald-100 border border-emerald-300"></div> Cố định</span>
        </div>
    </div>

    <div class="overflow-x-auto p-4 custom-scrollbar" style="max-height: calc(100vh - 280px);">
        @if($classes->count() > 0)
        <!-- Giao diện cuộn ngang cho bảng matrix -->
        <table class="w-full text-center border-collapse text-xs min-w-max">
            <thead class="sticky top-0 z-10">
                <tr>
                    <th class="bg-slate-100 border border-slate-200 p-2 font-black text-slate-700 min-w-[70px] shadow-sm sticky left-0 z-20">Lớp</th>
                    @for($d = $daysStart; $d <= $daysEnd; $d++)
                        @php $dayWidth = $periodsPerDay * 65; @endphp
                        <th colspan="{{ $periodsPerDay }}" class="bg-slate-800 text-white border border-slate-700 p-1.5 font-bold uppercase tracking-widest shadow-sm">
                            Thứ {{ $d }}
                        </th>
                    @endfor
                </tr>
                <tr>
                    <th class="bg-white border-b-2 border-r-2 border-slate-200 p-1 sticky left-0 z-20 shadow-sm"></th>
                    @for($d = $daysStart; $d <= $daysEnd; $d++)
                        @for($p = 1; $p <= $periodsPerDay; $p++)
                            <th class="bg-slate-50 border border-slate-200 py-1.5 px-1 font-bold text-slate-500 w-[65px]">
                                T{{ $p }}
                            </th>
                        @endfor
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($classes as $class)
                <tr class="hover:bg-slate-50/70 transition-colors group">
                    <td class="bg-white group-hover:bg-slate-50 border border-slate-200 p-2 font-bold text-blue-900 sticky left-0 z-10 shadow-sm whitespace-nowrap lg:whitespace-normal min-w-[70px]">
                        {{ $class->name }}
                        <div class="text-[9px] font-normal text-slate-400">Sĩ: {{ $class->student_count }}</div>
                    </td>
                    @for($d = $daysStart; $d <= $daysEnd; $d++)
                        @for($p = 1; $p <= $periodsPerDay; $p++)
                            @php
                                $sc = $matrixData[$class->id][$d][$p] ?? null;
                                $cellClass = 'bg-white border-slate-200 hover:bg-slate-50 cursor-pointer transition-colors';
                                if($sc) {
                                    if(str_contains(mb_strtolower($sc->subject->name), 'chào cờ') || str_contains(mb_strtolower($sc->subject->name), 'sinh hoạt')) {
                                        $cellClass = 'bg-emerald-50 border-emerald-200 hover:bg-emerald-100';
                                    } elseif($sc->subject->type == 2) {
                                        $cellClass = 'bg-purple-50 border-purple-200 hover:bg-purple-100';
                                    } else {
                                        $cellClass = 'bg-blue-50 border-blue-200 hover:bg-blue-100';
                                    }
                                }
                            @endphp
                            <td class="border p-1 align-top relative {{ $cellClass }} h-14 min-w-[65px] group/cell matrix-cell" 
                                data-class-id="{{ $class->id }}" data-day="{{ $d }}" data-period="{{ $p }}"
                                onclick="openAssignModal({{ $class->id }}, {{ $d }}, {{ $p }}, {{ $sc ? $sc->id : 'null' }})">
                                
                                @if($sc)
                                    <div class="draggable-item h-full w-full" data-id="{{ $sc->id }}" title="{{ $sc->subject->name }}\nGV: {{ $sc->teacher->name }}\nPhòng: {{ $sc->room->name ?? 'Mặc định' }}">
                                        <div class="font-bold text-slate-800 leading-tight line-clamp-1 overflow-hidden truncate">
                                            {{ mb_substr($sc->subject->name, 0, 7) }}{{ mb_strlen($sc->subject->name) > 7 ? '...' : '' }}
                                        </div>
                                        <div class="text-[9px] font-medium text-slate-600 mt-0.5 leading-tight truncate">
                                            {{ $sc->teacher->short_name ?? mb_substr($sc->teacher->name, 0, 8) }}
                                        </div>
                                        @if($sc->room_id)
                                            <div class="text-[8px] font-bold text-purple-700 bg-purple-100 rounded px-1 mt-0.5 inline-block truncate max-w-full">
                                                R.{{ $sc->room->name }}
                                            </div>
                                        @endif

                                        <!-- Thao tác nhanh (xóa) -->
                                        <div class="absolute top-0 right-0 p-0.5 opacity-0 group-hover/cell:opacity-100 transition-opacity">
                                            <button class="text-red-500 hover:bg-red-100 rounded p-0.5" onclick="event.stopPropagation(); deleteSchedule({{ $sc->id }})">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center opacity-0 group-hover/cell:opacity-100 transition-opacity pointer-events-none">
                                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </div>
                                @endif
                            </td>
                        @endfor
                    @endfor
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="py-12 text-center">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
            <p class="text-slate-500 font-medium">Không tìm thấy dữ liệu lớp học phù hợp với bộ lọc.</p>
        </div>
        @endif
    </div>
</div>

<!-- Modal Gán Tiết (Assign Modal) -->
<div id="assignModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800" id="modalTitle">Gán tiết học</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form id="assignForm" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="class_id" id="modal_class_id">
            <input type="hidden" name="day" id="modal_day">
            <input type="hidden" name="period" id="modal_period">

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Môn học</label>
                <select name="subject_id" id="modal_subject_id" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm font-medium">
                    <option value="">-- Chọn môn --</option>
                    @foreach ($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Giáo viên</label>
                <select name="teacher_id" id="modal_teacher_id" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm font-medium">
                    <option value="">-- Chọn giáo viên --</option>
                    @foreach ($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->short_code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Phòng (tùy chọn)</label>
                <select name="room_id" id="modal_room_id" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm font-medium">
                    <option value="">Mặc định (Sử dụng phòng của môn học)</option>
                    @foreach ($rooms as $r)
                        <option value="{{ $r->id }}">Phòng {{ $r->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-sm font-bold transition-all">Hủy</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-200 transition-all">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<style>
/* CSS cho thanh cuộn Ma trận bên trong */
.custom-scrollbar::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f8fafc;
    border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 8px;
    border: 2px solid #f8fafc;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.draggable-item {
    cursor: grab;
}
.draggable-item:active {
    cursor: grabbing;
}
.matrix-cell.drag-over {
    background-color: #f1f5f9;
    border: 2px dashed #94a3b8;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';

    // Khởi tạo Drag & Drop
    document.querySelectorAll('.matrix-cell').forEach(el => {
        new Sortable(el, {
            group: 'timetable',
            animation: 150,
            draggable: '.draggable-item',
            onEnd: function (evt) {
                const itemEl = evt.item;
                const toEl = evt.to;
                const scheduleId = itemEl.getAttribute('data-id');
                const newDay = toEl.getAttribute('data-day');
                const newPeriod = toEl.getAttribute('data-period');
                
                // Nếu kéo sang ô đã có tiết khác thì cancel hoặc xử lý ghi đè (ở đây tạm cho phép move)
                if (toEl.children.length > 1) {
                    // Logic check ghi đè nếu cần
                }

                updateSchedulePosition(scheduleId, newDay, newPeriod);
            }
        });
    });

    function updateSchedulePosition(id, day, period) {
        fetch(`/admin/timetable/update/${id}`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ day, period })
        })
        .then(res => res.json())
        .then(data => {
            if(!data.success) alert('Có lỗi khi cập nhật vị trí.');
        });
    }

    function openAssignModal(classId, day, period, scheduleId) {
        event.stopPropagation();
        document.getElementById('modal_class_id').value = classId;
        document.getElementById('modal_day').value = day;
        document.getElementById('modal_period').value = period;
        document.getElementById('modalTitle').innerText = `Gán tiết học (Thứ ${day}, Tiết ${period})`;
        document.getElementById('assignModal').classList.remove('hidden');
        document.getElementById('assignModal').classList.add('flex');
    }

    function closeModal() {
        document.getElementById('assignModal').classList.add('hidden');
        document.getElementById('assignModal').classList.remove('flex');
    }

    document.getElementById('assignForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());

        fetch('/admin/timetable/assign', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                location.reload(); // Tải lại để cập nhật màu sắc và text
            } else {
                alert('Có lỗi xảy ra.');
            }
        });
    });

    function deleteSchedule(id) {
        if(confirm('Bạn có chắc chắn muốn xóa tiết học này?')) {
            fetch(`/admin/timetable/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    location.reload();
                }
            });
        }
    }
</script>
@endsection
