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
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ ($type === 'class' && $id == $c->id) ? 'selected' : '' }}>{{ $c->name }} (Sĩ số: {{ $c->student_count }})</option>
                    @endforeach
                </select>
            </div>

            <div class="w-full md:w-1/3" id="teacherSelectDiv" style="display: {{ $type === 'teacher' ? 'block' : 'none' }}">
                <label class="block text-sm font-bold text-slate-700 mb-2">Chọn Giáo viên</label>
                <select name="teacher_id" id="teacher_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:bg-white text-sm font-medium transition-colors">
                    <option value="">-- Chọn Giáo viên --</option>
                    @foreach($teachers as $t)
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
    <div class="content-card overflow-hidden print-container bg-white shadow-xl border-slate-200">
        <!-- Header cho bản In (Chuyên nghiệp) -->
        <div class="p-8 border-b-2 border-slate-800 print:p-0 print:border-none">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8 print:mb-4">
                <div class="text-center md:text-left flex-1">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest print:text-xs">SỞ GIÁO DỤC VÀ ĐÀO TẠO</h3>
                    <h2 class="text-lg font-black text-slate-800 uppercase print:text-sm">{{ \App\Models\Setting::get('school_name', 'TRƯỜNG THPT CHUYÊN ...') }}</h2>
                    <div class="w-20 h-0.5 bg-slate-300 mt-2 mx-auto md:mx-0"></div>
                </div>
                <div class="text-center md:text-right flex-1">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-widest print:text-xs">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h3>
                    <h2 class="text-sm font-black text-slate-800 print:text-[10px]">Độc lập - Tự do - Hạnh phúc</h2>
                    <div class="w-32 h-0.5 bg-slate-300 mt-2 mx-auto md:ml-auto"></div>
                </div>
            </div>

            <div class="text-center mb-6">
                <h1 class="text-3xl font-black text-blue-900 tracking-tighter uppercase print:text-2xl print:text-black">THỜI KHÓA BIỂU {{ $type === 'class' ? 'LỚP' : 'GIÁO VIÊN' }}: {{ $selectedName }}</h1>
                <p class="text-base text-slate-600 font-bold mt-2 print:text-sm">Năm học: {{ \App\Models\Setting::get('school_year', '2025-2026') }} | Áp dụng từ ngày: {{ date('d/m/Y') }}</p>
            </div>
        </div>
        
        <div class="p-8 print:p-0">
            <table class="w-full border-collapse border-2 border-slate-800 print:border-black">
                <thead>
                    <tr class="bg-slate-800 text-white print:bg-gray-100 print:text-black">
                        <th class="border border-slate-600 print:border-black p-3 w-16 text-center font-black uppercase text-xs">Ca</th>
                        <th class="border border-slate-600 print:border-black p-3 w-12 text-center font-black uppercase text-xs">Tiết</th>
                        @for($d = $daysStart; $d <= $daysEnd; $d++)
                            <th class="border border-slate-600 print:border-black p-3 text-center font-black text-sm uppercase tracking-wider w-1/6">Thứ {{ $d }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    <!-- Sáng -->
                    @php $morningLimit = \App\Models\Setting::lunchAfterPeriod(); @endphp
                    @for($p = 1; $p <= $morningLimit; $p++)
                    <tr class="h-20 print:h-16">
                        @if($p == 1)
                            <td rowspan="{{ $morningLimit }}" class="border border-slate-300 print:border-black text-center font-black text-slate-700 bg-slate-50 print:bg-white align-middle" style="writing-mode: vertical-rl; text-orientation: upright;">SÁNG</td>
                        @endif
                        <td class="border border-slate-300 print:border-black text-center font-black text-slate-400 bg-slate-50/50 print:bg-white">{{ $p }}</td>
                        @for($d = $daysStart; $d <= $daysEnd; $d++)
                            @php $schedules = $timetableData[$d][$p] ?? []; @endphp
                            <td class="border border-slate-300 print:border-black p-2 align-middle text-center relative {{ count($schedules) > 1 ? 'bg-red-50' : '' }}">
                                @foreach($schedules as $sc)
                                    <div class="flex flex-col justify-center">
                                        <div class="font-black text-slate-800 text-sm print:text-xs leading-tight">
                                            {{ $sc->subject->name }}
                                        </div>
                                        <div class="text-[10px] font-bold text-slate-500 mt-1 uppercase">
                                            @if($type === 'class')
                                                {{ $sc->teacher->short_code ?? $sc->teacher->name }}
                                            @else
                                                Lớp {{ $sc->classRoom->name }}
                                            @endif
                                            @if($sc->room_id)
                                                <span class="mx-1">•</span> P. {{ $sc->room->name }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </td>
                        @endfor
                    </tr>
                    @endfor

                    <tr class="h-4 bg-slate-100 print:bg-gray-100">
                        <td colspan="{{ $daysEnd - $daysStart + 3 }}" class="border border-slate-300 print:border-black text-[8px] font-black text-slate-400 text-center uppercase tracking-[1em]">Nghỉ trưa</td>
                    </tr>

                    <!-- Chiều -->
                    @php $afternoonLimit = \App\Models\Setting::periodsPerDay(); @endphp
                    @for($p = $morningLimit + 1; $p <= $afternoonLimit; $p++)
                    <tr class="h-20 print:h-16">
                        @if($p == $morningLimit + 1)
                            <td rowspan="{{ $afternoonLimit - $morningLimit }}" class="border border-slate-300 print:border-black text-center font-black text-slate-700 bg-slate-50 print:bg-white align-middle" style="writing-mode: vertical-rl; text-orientation: upright;">CHIỀU</td>
                        @endif
                        <td class="border border-slate-300 print:border-black text-center font-black text-slate-400 bg-slate-50/50 print:bg-white">{{ $p }}</td>
                        @for($d = $daysStart; $d <= $daysEnd; $d++)
                            @php $schedules = $timetableData[$d][$p] ?? []; @endphp
                            <td class="border border-slate-300 print:border-black p-2 align-middle text-center relative {{ count($schedules) > 1 ? 'bg-red-50' : '' }}">
                                @foreach($schedules as $sc)
                                    <div class="flex flex-col justify-center">
                                        <div class="font-black text-slate-800 text-sm print:text-xs leading-tight">
                                            {{ $sc->subject->name }}
                                        </div>
                                        <div class="text-[10px] font-bold text-slate-500 mt-1 uppercase">
                                            @if($type === 'class')
                                                {{ $sc->teacher->short_code ?? $sc->teacher->name }}
                                            @else
                                                Lớp {{ $sc->classRoom->name }}
                                            @endif
                                            @if($sc->room_id)
                                                <span class="mx-1">•</span> P. {{ $sc->room->name }}
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </td>
                        @endfor
                    </tr>
                    @endfor
                </tbody>
            </table>

            <!-- Khu vực chữ ký -->
            <div class="mt-12 hidden print:grid grid-cols-2 gap-8 text-center">
                <div class="flex flex-col items-center">
                    <p class="text-sm font-bold uppercase mb-20 text-black">NGƯỜI LẬP BIỂU</p>
                    <p class="text-sm font-black text-black">(Ký và ghi rõ họ tên)</p>
                </div>
                <div class="flex flex-col items-center">
                    <p class="text-sm font-bold uppercase mb-4 text-black">HIỆU TRƯỞNG</p>
                    <p class="text-xs italic mb-16 text-black">(Ký tên và đóng dấu)</p>
                    <p class="text-sm font-black text-black">{{ \App\Models\Setting::get('principal_name', '..........................................') }}</p>
                </div>
            </div>
            
            <div class="mt-8 text-[10px] text-slate-400 font-medium italic print:mt-12">
                * Ghi chú: Thời khóa biểu có thể thay đổi tùy theo tình hình thực tế của nhà trường.
                <br>In lúc: {{ date('H:i d/m/Y') }} bởi Hệ thống Quản lý TKB Smart-Schedule.
            </div>
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
