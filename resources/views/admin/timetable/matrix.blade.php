@extends('admin.layouts.app')

@section('title', 'Ma trận Xếp Lịch TKB')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Ma trận Xếp Lịch TKB</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Quan sát & điểu chỉnh cục diện Thời khóa biểu toàn trường</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.timetable.matrix') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[13px] font-bold flex items-center gap-2 transition-colors">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Tải lại
        </a>
        <a href="{{ route('admin.timetable.export', ['grade' => $gradeFilter, 'shift' => $shiftFilter]) }}" target="_blank" class="px-3 py-2 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg text-[13px] font-bold flex items-center gap-2 transition-colors border border-blue-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            In ma trận (A3)
        </a>
        <form action="{{ route('admin.timetable.auto') }}" method="POST" onsubmit="return confirm('Hành động này sẽ XÓA TOÀN BỘ TKB hiện tại (trừ Tiết cố định) và tự động xếp lại. Bạn có chắc chắn không?');">
            @csrf
            <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[13px] font-bold flex items-center gap-2 shadow-lg shadow-emerald-200 transition-all border border-emerald-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                Xếp tự động
            </button>
        </form>
    </div>
</div>

<div class="content-card mb-6">
    <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <form action="{{ route('admin.timetable.matrix') }}" method="GET" class="w-full flex-1 flex flex-wrap gap-3">
            <select name="grade" class="px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white text-sm font-bold" onchange="this.form.submit()">
                <option value="">Khối: Tất cả</option>
                <option value="10" {{ $gradeFilter == '10' ? 'selected' : '' }}>Khối 10</option>
                <option value="11" {{ $gradeFilter == '11' ? 'selected' : '' }}>Khối 11</option>
                <option value="12" {{ $gradeFilter == '12' ? 'selected' : '' }}>Khối 12</option>
            </select>
            <select name="shift" class="px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white text-sm font-bold" onchange="this.form.submit()">
                <option value="">Ca: Tất cả</option>
                <option value="morning" {{ $shiftFilter == 'morning' ? 'selected' : '' }}>Sáng</option>
                <option value="afternoon" {{ $shiftFilter == 'afternoon' ? 'selected' : '' }}>Chiều</option>
            </select>

            <div class="h-8 w-px bg-slate-200 mx-1 hidden sm:block"></div>

            <select name="class_id" class="px-4 py-2 border-2 border-blue-200 rounded-lg focus:outline-none focus:border-blue-500 bg-blue-50 focus:bg-white text-sm font-black text-blue-800" onchange="this.form.submit()">
                @forelse($classes as $c)
                    <option value="{{ $c->id }}" {{ ($selectedClass->id ?? '') == $c->id ? 'selected' : '' }}>Lớp {{ $c->name }}</option>
                @empty
                    <option value="">-- Chưa có lớp --</option>
                @endforelse
            </select>
            
            @if($gradeFilter || $shiftFilter)
                <a href="{{ route('admin.timetable.matrix') }}" class="px-3 py-2 text-slate-400 hover:text-red-500 text-xs font-bold transition-colors">
                    HỦY LỌC
                </a>
            @endif
        </form>
        <div class="text-xs font-bold text-slate-500 flex gap-4 bg-slate-50 px-4 py-2 rounded-xl">
            <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-blue-100 border border-blue-400"></div> Lý thuyết</span>
            <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-purple-100 border border-purple-400"></div> Thực hành</span>
            <span class="flex items-center gap-1.5"><div class="w-3 h-3 rounded-full bg-emerald-100 border border-emerald-400"></div> Cố định</span>
        </div>
    </div>

<div class="flex flex-col lg:flex-row gap-6 p-4">
    <!-- Cột TRÁI: Kho thẻ (Card Pool) -->
    <div class="w-full lg:w-72 shrink-0">
        <div class="bg-slate-50 rounded-2xl border border-slate-200 overflow-hidden sticky top-6">
            <div class="p-4 border-b border-slate-200 bg-white flex justify-between items-center">
                <h2 class="text-xs font-black text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <div class="w-2 h-5 bg-blue-600 rounded-full"></div>
                    Kho thẻ môn học
                </h2>
                <span class="px-2 py-0.5 bg-blue-600 text-white text-[10px] font-black rounded-lg">
                    {{ collect($cardPool)->sum('count') }} tiết
                </span>
            </div>
            
            <div class="p-3 max-h-[calc(100vh-280px)] overflow-y-auto custom-scrollbar space-y-2">
                @if($selectedClass)
                    @forelse($cardPool as $card)
                        <div class="card-item p-3 bg-white border border-slate-200 rounded-xl shadow-sm cursor-grab hover:border-blue-400 hover:shadow-md transition-all group relative"
                            draggable="true" 
                            data-type="pool"
                            data-class-id="{{ $selectedClass->id }}"
                            data-subject-id="{{ $card['subject_id'] }}"
                            data-teacher-id="{{ $card['teacher_id'] }}"
                            data-subject-type="{{ $card['subject_type'] }}">
                            
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-xs font-black text-slate-800 leading-tight">{{ $card['subject_name'] }}</span>
                                <span class="bg-blue-50 text-blue-700 text-[10px] font-black px-1.5 py-0.5 rounded-lg border border-blue-100">x{{ $card['count'] }}</span>
                            </div>
                            <div class="text-[10px] font-bold text-slate-500 flex items-center gap-1.5">
                                <div class="w-4 h-4 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 uppercase text-[8px]">
                                    {{ substr($card['teacher_name'], 0, 1) }}
                                </div>
                                {{ $card['teacher_name'] }}
                            </div>

                            @if($card['subject_type'] == 2)
                                <div class="mt-2">
                                    <span class="text-[8px] font-black uppercase px-2 py-0.5 bg-purple-100 text-purple-600 rounded-full border border-purple-200">Phòng Thực hành</span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-wider">Đã xếp hết tiết!</p>
                        </div>
                    @endforelse
                @else
                    <div class="py-12 text-center text-slate-400">
                        <p class="text-xs font-bold uppercase">Chưa chọn lớp</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Cột PHẢI: Ma trận TKB Lớp -->
    <div class="flex-1 min-w-0">
        <div class="bg-white rounded-2xl border-2 border-slate-200 overflow-hidden shadow-sm">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h2 class="text-sm font-black text-blue-900 uppercase tracking-tighter flex items-center gap-2">
                    Thời khóa biểu chi tiết: Lớp {{ $selectedClass->name ?? '---' }}
                </h2>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase">Trạng thái:</span>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-black rounded-lg border border-emerald-200 italic">Đã đồng bộ</span>
                </div>
            </div>
            
            <div class="overflow-x-auto p-4 custom-scrollbar">
                @if($selectedClass)
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="w-16 p-2 bg-slate-100 border border-slate-200 rounded-tl-xl font-black text-slate-500 text-[10px] uppercase">Tiết</th>
                            @for($d = $daysStart; $d <= $daysEnd; $d++)
                                <th class="p-3 bg-slate-800 text-white border border-slate-700 font-extrabold text-sm uppercase tracking-widest {{ $d == $daysEnd ? 'rounded-tr-xl' : '' }}">
                                    Thứ {{ $d }}
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @for($p = 1; $p <= $periodsPerDay; $p++)
                        <tr class="{{ $p % 5 == 0 ? 'border-b-4 border-slate-200' : '' }}">
                            <td class="p-2 border border-slate-200 bg-slate-100 font-black text-slate-600 text-center text-base">
                                {{ $p }}
                            </td>
                            @for($d = $daysStart; $d <= $daysEnd; $d++)
                                @php
                                    $sc = $matrixData[$d][$p] ?? null;
                                    $cellClass = 'bg-white border-slate-200 hover:bg-slate-50';
                                    if($sc) {
                                        if($sc->subject->is_fixed) $cellClass = 'bg-emerald-50 border-emerald-200 hover:bg-emerald-100';
                                        elseif($sc->subject->type == 2) $cellClass = 'bg-purple-50 border-purple-200 hover:bg-purple-100';
                                        else $cellClass = 'bg-blue-50 border-blue-200 hover:bg-blue-100';
                                    }
                                @endphp
                                <td class="border-2 p-2 align-top relative {{ $cellClass }} h-24 w-1/6 matrix-cell group/cell transition-all" 
                                    data-class-id="{{ $selectedClass->id }}" data-day="{{ $d }}" data-period="{{ $p }}">
                                    
                                    @if($sc)
                                        <div class="draggable-item h-full flex flex-col justify-between" 
                                            draggable="true" data-type="matrix" data-id="{{ $sc->id }}" 
                                            data-teacher-id="{{ $sc->teacher_id }}"
                                            data-subject-type="{{ $sc->subject->type }}">
                                            
                                            <div>
                                                <div class="font-black text-blue-900 leading-tight text-xs mb-1">
                                                    {{ $sc->subject->name }}
                                                </div>
                                                <div class="text-[10px] font-bold text-slate-500 flex items-center gap-1">
                                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    {{ $sc->teacher->short_name ?? $sc->teacher->name }}
                                                </div>
                                            </div>

                                            <div class="mt-auto flex justify-between items-end">
                                                @if($sc->room_id)
                                                    <span class="text-[9px] font-black bg-purple-600 text-white px-1.5 py-0.5 rounded shadow-sm">
                                                        P.{{ $sc->room->name }}
                                                    </span>
                                                @else
                                                    <span></span>
                                                @endif

                                                <button class="opacity-0 group-hover/cell:opacity-100 text-red-400 hover:text-red-700 transition-all p-1" 
                                                    onclick="deleteSchedule({{ $sc->id }})">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                        @endfor
                    </tbody>
                </table>
                @else
                <div class="py-20 text-center">
                    <p class="text-slate-400 font-bold uppercase tracking-widest italic">Vui lòng tạo lớp học trong hệ thống trước.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
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
                    @foreach($subjects as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Giáo viên</label>
                <select name="teacher_id" id="modal_teacher_id" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm font-medium">
                    <option value="">-- Chọn giáo viên --</option>
                    @foreach($teachers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }} ({{ $t->short_code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Phòng (tùy chọn)</label>
                <select name="room_id" id="modal_room_id" class="w-full px-4 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all text-sm font-medium">
                    <option value="">Mặc định (Sử dụng phòng của môn học)</option>
                    @foreach($rooms as $r)
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
    border: 2px dashed #3b82f6 !important;
}
.matrix-cell.busy-highlight {
    background-image: repeating-linear-gradient(45deg, #f1f5f9, #f1f5f9 10px, #e2e8f0 10px, #e2e8f0 20px);
    opacity: 0.7;
    cursor: not-allowed;
}
</style>

<!-- Modal Chọn Phòng (Room Selection Modal) -->
<div id="roomModal" class="fixed inset-0 z-[110] hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden border border-slate-200">
        <div class="px-6 py-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Chọn Phòng Thực Hành</h3>
            <button onclick="closeRoomModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="p-6">
            <p class="text-[11px] text-slate-500 mb-4 font-medium leading-relaxed">Môn này yêu cầu phòng chức năng. Vui lòng chọn một phòng còn trống:</p>
            <div id="roomList" class="grid grid-cols-1 gap-2 max-h-60 overflow-y-auto custom-scrollbar p-1">
                <!-- Danh sách phòng sẽ được render bằng JS -->
            </div>
            <div class="mt-6 flex gap-3">
                <button onclick="closeRoomModal()" class="flex-1 px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-200 transition-all uppercase tracking-wider">Hủy</button>
            </div>
        </div>
    </div>
</div>

<script>
    const CSRF_TOKEN = '{{ csrf_token() }}';
    let draggedData = null;

    // Khởi tạo các sự kiện Drag & Drop HTML5
    function initDragAndDrop() {
        const draggables = document.querySelectorAll('[draggable="true"]');
        const cells = document.querySelectorAll('.matrix-cell');

        draggables.forEach(item => {
            item.addEventListener('dragstart', handleDragStart);
            item.addEventListener('dragend', handleDragEnd);
        });

        cells.forEach(cell => {
            cell.addEventListener('dragover', handleDragOver);
            cell.addEventListener('dragleave', handleDragLeave);
            cell.addEventListener('drop', handleDrop);
        });
    }

    function handleDragStart(e) {
        this.classList.add('opacity-40', 'scale-95');
        draggedData = {
            type: this.dataset.type, // 'pool' hoặc 'matrix'
            id: this.dataset.id,     // Chỉ có nếu type='matrix'
            classId: this.dataset.classId,
            subjectId: this.dataset.subjectId,
            teacherId: this.dataset.teacherId,
            subjectType: this.dataset.subjectType
        };
        e.dataTransfer.setData('text/plain', ''); // Firefox fix
        e.dataTransfer.effectAllowed = 'move';

        // Highlighting teacher busy slots
        if (draggedData.teacherId) {
            highlightTeacherBusySlots(draggedData.teacherId);
        }
    }

    function handleDragEnd(e) {
        this.classList.remove('opacity-40', 'scale-95');
        clearHighlights();
    }

    async function highlightTeacherBusySlots(teacherId) {
        try {
            const response = await fetch(`/admin/api/teachers/${teacherId}/busy-slots`);
            const data = await response.json();
            const busySlots = data.busy; // Array of "day-period"

            const cells = document.querySelectorAll('.matrix-cell');
            cells.forEach(cell => {
                const slotKey = `${cell.dataset.day}-${cell.dataset.period}`;
                if (busySlots.includes(slotKey)) {
                    cell.classList.add('busy-highlight');
                }
            });
        } catch (error) {
            console.error('Error fetching busy slots:', error);
        }
    }

    function clearHighlights() {
        document.querySelectorAll('.matrix-cell').forEach(cell => {
            cell.classList.remove('busy-highlight', 'bg-blue-50/50', 'border-blue-400', 'border-dashed');
        });
    }

    function handleDragOver(e) {
        e.preventDefault();
        this.classList.add('bg-blue-50/50', 'border-blue-400', 'border-dashed');
        return false;
    }

    function handleDragLeave(e) {
        this.classList.remove('bg-blue-50/50', 'border-blue-400', 'border-dashed');
    }

    async function handleDrop(e) {
        e.preventDefault();
        this.classList.remove('bg-blue-50/50', 'border-blue-400', 'border-dashed');
        
        if (!draggedData) return;

        const targetClassId = this.dataset.classId;
        const targetDay = this.dataset.day;
        const targetPeriod = this.dataset.period;

        // 1. Kiểm tra nếu type='pool', class phải khớp (trừ khi cho phép đa lớp)
        if (draggedData.type === 'pool' && draggedData.classId != targetClassId) {
            showToast('Thẻ này thuộc về lớp khác!', 'error');
            return;
        }

        // 2. Nếu subjects type=2 (Thực hành) → Mở modal chọn phòng
        if (draggedData.subjectType == 2) {
            openRoomModal(targetClassId, targetDay, targetPeriod);
            return;
        }

        // 3. Xử lý Logic Backend: Swap hoặc Assign
        if (draggedData.type === 'matrix') {
            // Swap if target cell has item, else Move
            executeSwapOrMove(draggedData.id, targetClassId, targetDay, targetPeriod);
        } else {
            // Assign from Pool
            executeAssign(draggedData, targetClassId, targetDay, targetPeriod);
        }
    }

    async function executeSwapOrMove(id, targetClassId, day, period) {
        try {
            const response = await fetch('/admin/timetable/swap', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    schedule_id: id,
                    target_day: day,
                    target_period: period,
                    target_class_id: targetClassId
                })
            });
            const data = await response.json();
            if (data.success) {
                showToast(data.swapped ? 'Đã hoán đổi vị trí!' : 'Đã di chuyển tiết học!');
                location.reload();
            } else {
                showToast(data.message || 'Xung đột lịch diễn ra!', 'error');
            }
        } catch (error) {
            console.error(error);
        }
    }

    async function executeAssign(payload, classId, day, period, roomId = null) {
        try {
            const response = await fetch('/admin/timetable/assign', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    class_id: classId,
                    day: day,
                    period: period,
                    subject_id: payload.subjectId,
                    teacher_id: payload.teacherId,
                    room_id: roomId
                })
            });
            const data = await response.json();
            if (data.success) {
                showToast('Đã gán tiết học thành công!');
                location.reload();
            } else {
                showToast(data.message || 'Lỗi khi gán lịch!', 'error');
            }
        } catch (error) {
            console.error(error);
        }
    }

    // Modal Logic
    let pendingAssign = null;
    function openRoomModal(classId, day, period) {
        pendingAssign = { classId, day, period };
        const list = document.getElementById('roomList');
        list.innerHTML = '<div class="text-center py-4 text-xs text-slate-400">Đang tải danh sách phòng...</div>';
        document.getElementById('roomModal').classList.remove('hidden');
        document.getElementById('roomModal').classList.add('flex');

        // Fetch available rooms for this slot
        fetch(`/api/rooms/available?day=${day}&period=${period}&subject_id=${draggedData.subjectId}`)
            .then(res => res.json())
            .then(data => {
                list.innerHTML = '';
                if (data.length === 0) {
                    list.innerHTML = '<div class="col-span-full py-4 text-center text-xs text-red-500 font-bold uppercase">Hết phòng thực hành khả dụng!</div>';
                    return;
                }
                data.forEach(room => {
                    const btn = document.createElement('button');
                    btn.className = 'w-full flex justify-between items-center p-3 bg-slate-50 border border-slate-200 rounded-xl hover:bg-blue-50 hover:border-blue-300 transition-all text-left group';
                    btn.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-xs font-black text-slate-800">Phòng ${room.name}</span>
                                <span class="text-[9px] text-slate-500 uppercase font-bold">${room.category_name}</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    `;
                    btn.onclick = () => {
                        if (draggedData.type === 'matrix') {
                             // This part is complex, for simplicity we skip updating room during matrix swap for now or handle later
                             executeSwapOrMove(draggedData.id, pendingAssign.classId, pendingAssign.day, pendingAssign.period);
                        } else {
                             executeAssign(draggedData, pendingAssign.classId, pendingAssign.day, pendingAssign.period, room.id);
                        }
                        closeRoomModal();
                    };
                    list.appendChild(btn);
                });
            });
    }

    function closeRoomModal() {
        document.getElementById('roomModal').classList.add('hidden');
        document.getElementById('roomModal').classList.remove('flex');
    }

    function deleteSchedule(id) {
        if (!confirm('Bạn có chắc chắn muốn xóa tiết học này khỏi ma trận?')) return;
        fetch(`/admin/timetable/delete/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
        }).then(res => res.json()).then(data => location.reload());
    }

    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-6 right-6 px-6 py-3 rounded-2xl shadow-xl z-[200] flex items-center gap-3 transform transition-all duration-300 translate-y-20 ${type === 'success' ? 'bg-blue-900 text-white' : 'bg-red-600 text-white'}`;
        toast.innerHTML = `
            <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center">
                ${type === 'success' ? '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>' : '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>'}
            </div>
            <span class="text-sm font-black whitespace-nowrap tracking-wide uppercase">${message}</span>
        `;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.remove('translate-y-20'), 10);
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Khởi tạo
    document.addEventListener('DOMContentLoaded', initDragAndDrop);
</script>
@endsection
