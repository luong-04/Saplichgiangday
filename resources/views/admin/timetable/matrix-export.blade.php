@extends('admin.layouts.app')

@section('title', 'Xuất Ma trận TKB')

@section('content')
<div class="mb-6 flex justify-between items-center print:hidden">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Xuất Ma trận TKB</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Bản in tổng hợp toàn khối / toàn trường</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('admin.timetable.matrix', request()->all()) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-bold transition-colors">
            Quay lại Ma trận
        </a>
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold flex items-center gap-2 shadow-lg shadow-blue-200 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            In bản tổng hợp
        </button>
    </div>
</div>

<div class="print-container bg-white p-4 sm:p-8 shadow-xl rounded-2xl border border-slate-200 print:shadow-none print:border-none print:p-0">
    <!-- Header Chuyên nghiệp -->
    <div class="mb-8 print:mb-6">
        <div class="flex justify-between items-start mb-6">
            <div class="text-center">
                <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest print:text-[8px]">SỞ GIÁO DỤC VÀ ĐÀO TẠO</h3>
                <h2 class="text-xs font-black text-slate-800 uppercase print:text-[10px]">{{ \App\Models\Setting::get('school_name', 'TRƯỜNG THPT CHUYÊN ...') }}</h2>
                <div class="w-12 h-0.5 bg-slate-300 mt-1 mx-auto"></div>
            </div>
            <div class="text-center">
                <h3 class="text-[10px] font-bold text-slate-500 uppercase tracking-widest print:text-[8px]">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</h3>
                <h2 class="text-[10px] font-black text-slate-800">Độc lập - Tự do - Hạnh phúc</h2>
                <div class="w-20 h-0.5 bg-slate-300 mt-1 mx-auto"></div>
            </div>
        </div>

        <div class="text-center">
            <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight print:text-xl">
                BẢNG TỔNG HỢP THỜI KHÓA BIỂU 
                @if($gradeFilter) KHỐI {{ $gradeFilter }} @else TOÀN TRƯỜNG @endif
            </h1>
            <p class="text-sm font-bold text-slate-600 mt-1">
                Năm học: {{ \App\Models\Setting::get('school_year', '2025-2026') }} | 
                @if($shiftFilter) Buổi: {{ $shiftFilter === 'morning' ? 'Sáng' : 'Chiều' }} | @endif
                Áp dụng từ ngày: {{ date('d/m/Y') }}
            </p>
        </div>
    </div>

    <!-- Table Ma trận Hiển thị cao (High-Density) -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse border-[1px] border-slate-800 print:border-black">
            <thead>
                <tr class="bg-slate-50 print:bg-gray-100">
                    <th class="border border-slate-800 print:border-black p-1 text-[9px] font-black uppercase w-8">Thứ</th>
                    <th class="border border-slate-800 print:border-black p-1 text-[9px] font-black uppercase w-6">T</th>
                    @foreach($classes as $class)
                        <th class="border border-slate-800 print:border-black p-1 text-[10px] font-black uppercase min-w-[60px] whitespace-nowrap bg-blue-50/50 print:bg-gray-50">
                            {{ $class->name }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @for($d = $daysStart; $d <= $daysEnd; $d++)
                    @for($p = 1; $p <= $periodsPerDay; $p++)
                        <tr class="{{ $p == \App\Models\Setting::lunchAfterPeriod() ? 'border-b-2 border-slate-800 print:border-black' : '' }}">
                            @if($p == 1)
                                <td rowspan="{{ $periodsPerDay }}" class="border border-slate-800 print:border-black text-center font-black text-xs bg-slate-50 print:bg-white align-middle">
                                    {{ $d }}
                                </td>
                            @endif
                            <td class="border border-slate-800 print:border-black text-center text-[9px] font-bold text-slate-400 bg-slate-50/30 print:bg-white p-0.5">
                                {{ $p }}
                            </td>
                            @foreach($classes as $class)
                                @php $schedules = $matrixData[$class->id][$d][$p] ?? []; @endphp
                                <td class="border border-slate-800 print:border-black h-10 p-0.5 text-center align-middle hover:bg-yellow-50/30 transition-colors">
                                    @foreach($schedules as $sc)
                                        <div class="flex flex-col gap-0">
                                            <div class="text-[9px] font-black text-slate-800 leading-tight truncate px-0.5">
                                                {{ $sc->subject->name }}
                                            </div>
                                            <div class="text-[8px] font-bold text-slate-500 uppercase flex justify-center gap-1">
                                                <span>{{ $sc->teacher->short_code ?? substr($sc->teacher->name, 0, 3) }}</span>
                                                @if($sc->room_id)
                                                    <span class="text-blue-600">[{{ $sc->room->name }}]</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </td>
                            @endforeach
                        </tr>
                    @endfor
                    <!-- Dòng ngăn cách giữa các ngày -->
                    <tr class="h-1 bg-slate-800 print:bg-black">
                        <td colspan="{{ count($classes) + 2 }}"></td>
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <!-- Footnote & Signatures -->
    <div class="mt-8 grid grid-cols-2 gap-8 text-center print:mt-6">
        <div class="flex flex-col items-center">
            <p class="text-[10px] font-bold uppercase mb-16 text-black">Người lập biểu</p>
            <p class="text-[9px] font-black text-black">(Ký và ghi rõ họ tên)</p>
        </div>
        <div class="flex flex-col items-center">
            <p class="text-[10px] font-bold uppercase mb-2 text-black">Hiệu trưởng</p>
            <p class="text-[8px] italic mb-12 text-black">(Ký tên và đóng dấu)</p>
            <p class="text-[10px] font-black text-black">{{ \App\Models\Setting::get('principal_name', '..........................................') }}</p>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    .print-container, .print-container * { visibility: visible; }
    .print-container {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    @page {
        size: A3 landscape;
        margin: 0.5cm;
    }
    /* Giảm font size tối đa cho bản in A3 nhiều cột */
    table { font-size: 8px !important; }
}
</style>
@endsection
