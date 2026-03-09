@extends('admin.layouts.app')

@section('title', 'Tra cứu Thời khóa biểu')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Tra cứu Thời khóa biểu</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Xem lịch giảng dạy của từng lớp hoặc từng giáo viên</p>
    </div>
    @if(isset($selectedName))
    <button onclick="window.print()" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-bold flex items-center gap-2 shadow-lg shadow-slate-200 transition-all print:hidden">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
        In Thời khóa biểu
    </button>
    @endif
</div>

<div class="content-card mb-6 print:hidden">
    <div class="p-5 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
        <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Bộ lọc Tra cứu
        </h2>
    </div>
    <div class="p-6">
        <form action="{{ route('admin.timetable.view') }}" method="GET" class="flex flex-col md:flex-row items-end gap-5">
            <div class="w-full md:w-1/3">
                <label class="block text-sm font-bold text-slate-700 mb-2">Đang xem theo:</label>
                <div class="flex p-1 bg-slate-100 rounded-lg">
                    <label class="flex-1 text-center font-bold text-sm cursor-pointer relative">
                        <input type="radio" name="type" value="class" class="peer sr-only" {{ $type === 'class' ? 'checked' : '' }} onchange="toggleSelects()">
                        <div class="py-2.5 rounded-md text-slate-500 peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-sm transition-all shadow-slate-200">
                            Học sinh (Lớp)
                        </div>
                    </label>
                    <label class="flex-1 text-center font-bold text-sm cursor-pointer relative">
                        <input type="radio" name="type" value="teacher" class="peer sr-only" {{ $type === 'teacher' ? 'checked' : '' }} onchange="toggleSelects()">
                        <div class="py-2.5 rounded-md text-slate-500 peer-checked:bg-white peer-checked:text-blue-700 peer-checked:shadow-sm transition-all shadow-slate-200">
                            Giáo viên
                        </div>
                    </label>
                </div>
            </div>

            <div class="w-full md:w-1/3" id="classSelectDiv" style="display: {{ $type === 'class' ? 'block' : 'none' }}">
                <label class="block text-sm font-bold text-slate-700 mb-2">Chọn Lớp học</label>
                <select name="class_id" id="class_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:bg-white text-sm font-medium transition-colors">
                    <option value="">-- Chọn Lớp --</option>
                    @foreach ($classes as $c)
                        <option value="{{ $c->id }}" {{ ($type === 'class' && $id == $c->id) ? 'selected' : '' }}>{{ $c->name }} (Sĩ số: {{ $c->student_count }})</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-1/3" id="teacherSelectDiv" style="display: {{ $type === 'teacher' ? 'block' : 'none' }}">
                <label class="block text-sm font-bold text-slate-700 mb-2">Chọn Giáo viên</label>
                <select name="teacher_id" id="teacher_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:bg-white text-sm font-medium transition-colors">
                    <option value="">-- Chọn Giáo viên --</option>
                    @foreach ($teachers as $t)
                        <option value="{{ $t->id }}" {{ ($type === 'teacher' && $id == $t->id) ? 'selected' : '' }}>{{ $t->name }} ({{ $t->short_code ?? $t->lookup_code }})</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-colors">
                Xem TKB
            </button>
        </form>
    </div>
</div>

@if($selectedName)
    <div class="content-card overflow-hidden print-container bg-white">
        <!-- Header cho bản In -->
        <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-blue-50/50 print:bg-white print:border-b-2 print:border-black">
            <div class="print-header">
                <h1 class="text-2xl print:text-xl font-extrabold text-blue-900 tracking-tight uppercase print:text-black">THỜI KHÓA BIỂU {{ $selectedName }}</h1>
                <p class="text-sm text-blue-700 font-semibold mt-1 print:text-black">Năm học: {{ \App\Models\Setting::get('school_year', '2023 - 2024') }} | Áp dụng từ: ...../...../.......</p>
            </div>
            <div class="w-16 h-16 rounded-xl bg-white border border-blue-100 flex items-center justify-center shadow-sm print:hidden">
                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        </div>
        
        <div class="p-6 print:p-0">
            <table class="w-full border-collapse text-sm print:text-xs">
                <thead>
                    <tr>
                        <th class="border-2 border-slate-200 print:border-black bg-slate-50 print:bg-gray-100 p-3 w-16 text-center text-slate-700 font-bold print:text-black uppercase">Buổi</th>
                        <th class="border-2 border-slate-200 print:border-black bg-slate-50 print:bg-gray-100 p-3 w-16 text-center text-slate-700 font-bold print:text-black uppercase">Tiết</th>
                        @for($d = $daysStart; $d <= $daysEnd; $d++)
                            <th class="border-2 border-slate-200 print:border-black bg-slate-800 text-white print:bg-gray-200 print:text-black p-3 text-center font-bold text-base print:text-sm uppercase tracking-wider w-1/6">Thứ {{ $d }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    <!-- Sáng -->
                    @php $morningLimit = 5; @endphp
                    @for($p = 1; $p <= $morningLimit; $p++)
                    <tr>
                        @if($p == 1)
                            <td rowspan="{{ $morningLimit }}" class="border-2 border-slate-200 print:border-black text-center font-black text-amber-700 bg-amber-50 print:bg-white align-middle print:text-black" style="writing-mode: vertical-rl; text-orientation: upright; letter-spacing: 0.2em;">SÁNG</td>
                        @endif
                        <td class="border-2 border-slate-200 print:border-black text-center font-bold text-slate-500 bg-slate-50 print:bg-white print:text-black">{{ $p }}</td>
                        @for($d = $daysStart; $d <= $daysEnd; $d++)
                            @php 
                                $schedules = $timetableData[$d][$p] ?? []; 
                            @endphp
                            <td class="border-2 border-slate-200 print:border-black p-2 align-top transition-colors {{ count($schedules) > 1 ? 'bg-red-50' : 'hover:bg-blue-50/30 print:bg-white' }}">
                                @foreach ($schedules as $sc)
                                    <div class="flex flex-col h-full bg-white print:bg-transparent rounded border border-slate-100 print:border-0 p-1.5 shadow-sm print:shadow-none {{ !$loop->last ? 'mb-2' : '' }} {{ count($schedules) > 1 ? 'border-red-300' : '' }}">
                                        <div class="font-bold text-indigo-900 print:text-black text-sm print:text-xs text-center mb-1 line-clamp-2">
                                            {{ $sc->subject->name }}
                                        </div>
                                        <div class="mt-auto flex justify-between items-center text-[10px] sm:text-xs">
                                            @if($type === 'class')
                                                <span class="text-slate-600 font-medium bg-slate-100 px-1 rounded truncate max-w-[60%]">{{ $sc->teacher->short_name ?? $sc->teacher->name }}</span>
                                            @else
                                                <span class="text-emerald-700 font-bold bg-emerald-100 px-1 rounded">{{ $sc->classRoom->name }}</span>
                                            @endif
                                            
                                            @if($sc->room_id)
                                                <span class="text-purple-700 font-bold bg-purple-100 px-1.5 rounded">{{ $sc->room->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </td>
                        @endfor
                    </tr>
                    @endfor

                    <!-- Chiều -->
                    @php $afternoonLimit = $periodsPerDay; @endphp
                    @if($afternoonLimit > $morningLimit)
                        <!-- Dòng phân cách nghỉ trưa -->
                        <tr>
                            <td colspan="{{ $daysEnd - $daysStart + 3 }}" class="border-y-4 border-slate-300 print:border-black bg-slate-100 print:bg-gray-100 py-1 text-center text-[10px] font-bold text-slate-400 uppercase tracking-[0.5em]">Giờ nghỉ trưa</td>
                        </tr>

                        @for($p = $morningLimit + 1; $p <= $afternoonLimit; $p++)
                        <tr>
                            @if($p == $morningLimit + 1)
                                <td rowspan="{{ $afternoonLimit - $morningLimit }}" class="border-2 border-slate-200 print:border-black text-center font-black text-indigo-700 bg-indigo-50 print:bg-white align-middle print:text-black" style="writing-mode: vertical-rl; text-orientation: upright; letter-spacing: 0.2em;">CHIỀU</td>
                            @endif
                            <td class="border-2 border-slate-200 print:border-black text-center font-bold text-slate-500 bg-slate-50 print:bg-white print:text-black">{{ $p }}</td>
                            @for($d = $daysStart; $d <= $daysEnd; $d++)
                                @php 
                                    $schedules = $timetableData[$d][$p] ?? []; 
                                @endphp
                                <td class="border-2 border-slate-200 print:border-black p-2 align-top transition-colors {{ count($schedules) > 1 ? 'bg-red-50' : 'hover:bg-blue-50/30 print:bg-white' }}">
                                    @foreach ($schedules as $sc)
                                        <div class="flex flex-col h-full bg-white print:bg-transparent rounded border border-slate-100 print:border-0 p-1.5 shadow-sm print:shadow-none {{ !$loop->last ? 'mb-2' : '' }} {{ count($schedules) > 1 ? 'border-red-300' : '' }}">
                                            <div class="font-bold text-indigo-900 print:text-black text-sm print:text-xs text-center mb-1 line-clamp-2">
                                                {{ $sc->subject->name }}
                                            </div>
                                            <div class="mt-auto flex justify-between items-center text-[10px] sm:text-xs">
                                                @if($type === 'class')
                                                    <span class="text-slate-600 font-medium bg-slate-100 px-1 rounded truncate max-w-[60%]">{{ $sc->teacher->short_name ?? $sc->teacher->name }}</span>
                                                @else
                                                    <span class="text-emerald-700 font-bold bg-emerald-100 px-1 rounded">{{ $sc->classRoom->name }}</span>
                                                @endif
                                                
                                                @if($sc->room_id)
                                                    <span class="text-purple-700 font-bold bg-purple-100 px-1.5 rounded">{{ $sc->room->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </td>
                            @endfor
                        </tr>
                        @endfor
                    @endif
                </tbody>
            </table>
        </div>
    </div>
@else
    @if($id) <!-- Đã submit nhưng không thấy data -->
        <div class="py-12 text-center bg-white rounded-xl border border-slate-200">
            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-slate-500 font-medium">Không tìm thấy thời khóa biểu cho đối tượng này.</p>
        </div>
    @else
        <div class="py-12 text-center bg-white rounded-xl border border-slate-200 border-dashed">
            <svg class="w-12 h-12 text-blue-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-slate-500 font-medium">Vui lòng chọn bộ lọc ở trên và nhấn "Xem TKB" để hiển thị dữ liệu.</p>
        </div>
    @endif
@endif

<style>
/* CSS cho bản in */
@media print {
    body * {
        visibility: hidden;
    }
    .print-container, .print-container * {
        visibility: visible;
    }
    .print-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        box-shadow: none !important;
        border: none !important;
    }
    @page {
        size: landscape;
        margin: 1cm;
    }
    .print-header {
        text-align: center;
        width: 100%;
        margin-bottom: 20px;
    }
}
</style>

<script>
    function toggleSelects() {
        const type = document.querySelector('input[name="type"]:checked').value;
        const classDiv = document.getElementById('classSelectDiv');
        const teacherDiv = document.getElementById('teacherSelectDiv');
        const classSelect = document.getElementById('class_id');
        const teacherSelect = document.getElementById('teacher_id');
        
        if (type === 'class') {
            classDiv.style.display = 'block';
            teacherDiv.style.display = 'none';
            teacherSelect.value = ''; // Reset
            
            // Xóa required nếu có
            classSelect.setAttribute('required', 'required');
            teacherSelect.removeAttribute('required');
        } else {
            classDiv.style.display = 'none';
            teacherDiv.style.display = 'block';
            classSelect.value = ''; // Reset
            
            // Xóa required nếu có
            teacherSelect.setAttribute('required', 'required');
            classSelect.removeAttribute('required');
        }
    }
    
    // Auto gọi khi load trang để set required đúng
    document.addEventListener("DOMContentLoaded", function() {
        toggleSelects();
    });
</script>
@endsection
