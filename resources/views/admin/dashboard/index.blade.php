@extends('admin.layouts.app')

@section('title', 'Bảng điều khiển')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Tổng quan hệ thống</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Xin chào, {{ auth()->user()->name ?? 'Quản trị viên' }}! Chúc bạn một ngày làm việc hiệu quả.</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="content-card p-6 flex flex-col justify-between relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-110 transition-transform duration-500 z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Giáo viên</span>
            </div>
            <div>
                <h3 class="text-3xl font-black text-slate-800">{{ $stats['teachers'] }}</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Giáo viên trong hệ thống</p>
            </div>
        </div>
    </div>

    <div class="content-card p-6 flex flex-col justify-between relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-indigo-50 rounded-full group-hover:scale-110 transition-transform duration-500 z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Lớp học</span>
            </div>
            <div>
                <h3 class="text-3xl font-black text-slate-800">{{ $stats['classes'] }}</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Lớp học hiện tại</p>
            </div>
        </div>
    </div>

    <div class="content-card p-6 flex flex-col justify-between relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 rounded-full group-hover:scale-110 transition-transform duration-500 z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Môn học</span>
            </div>
            <div>
                <h3 class="text-3xl font-black text-slate-800">{{ $stats['subjects'] }}</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Tổng cộng các môn</p>
            </div>
        </div>
    </div>

    <div class="content-card p-6 flex flex-col justify-between relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-110 transition-transform duration-500 z-0"></div>
        <div class="relative z-10">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Phòng học</span>
            </div>
            <div>
                <h3 class="text-3xl font-black text-slate-800">{{ $stats['rooms'] }}</h3>
                <p class="text-xs text-slate-500 font-medium mt-1">Phòng học khả dụng</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Hướng dẫn nhanh -->
    <div class="content-card p-6">
        <h3 class="text-lg font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Bước thiết lập TKB khuyên dùng
        </h3>
        
        <div class="space-y-4">
            <div class="flex gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center flex-shrink-0 text-sm">1</div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Cập nhật Môn học & Chương trình</h4>
                    <p class="text-xs text-slate-500 mt-1">Đảm bảo danh sách môn học và phân bổ số tiết cho từng khối lớp đã chính xác.</p>
                </div>
            </div>
            <div class="flex gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center flex-shrink-0 text-sm">2</div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Quản lý Giáo viên</h4>
                    <p class="text-xs text-slate-500 mt-1">Cập nhật thông tin giáo viên, gán môn dạy, lớp dạy, định mức tiết và thiết lập thời gian tránh dạy.</p>
                </div>
            </div>
            <div class="flex gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-bold flex items-center justify-center flex-shrink-0 text-sm">3</div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Thiết lập Tiết cố định</h4>
                    <p class="text-xs text-slate-500 mt-1">Gắn cứng các tiết sinh hoạt, chào cờ hoặc các môn yều cầu gắn phòng máy thuật / thể dục trước.</p>
                </div>
            </div>
            <div class="flex gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors border border-transparent hover:border-slate-100">
                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 font-bold flex items-center justify-center flex-shrink-0 text-sm">4</div>
                <div>
                    <h4 class="font-bold text-blue-800 text-sm">Xếp bằng Ma trận</h4>
                    <p class="text-xs text-blue-600/80 mt-1">Mở màn hình ma trận kéo thả và bắt đầu xếp các tiết còn lại cho toàn trường.</p>
                    <a href="{{ route('admin.timetable.matrix') }}" class="inline-block mt-2 text-xs font-bold text-blue-600 hover:text-blue-800 uppercase tracking-widest">Đến ma trận &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
