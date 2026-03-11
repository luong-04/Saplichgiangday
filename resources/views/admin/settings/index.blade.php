@extends('admin.layouts.app')

@section('title', 'Cấu hình Hệ thống')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Cấu hình Hệ thống</h1>
    <p class="text-sm text-slate-500 font-medium mt-1">Điều chỉnh các thông số thuật toán xếp thời khóa biểu và thông tin trường</p>
</div>

<form action="{{ route('admin.settings.store') }}" method="POST">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Thông tin chung -->
        <div class="content-card">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Thông tin Trường
                </h2>
            </div>
            <div class="p-6 space-y-5">
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="school_name">Tên trường học <span class="text-red-500">*</span></label>
                    <input type="text" id="school_name" name="school_name" value="{{ old('school_name', $settings['school_name']->value ?? '') }}" required
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="principal_name">Hiệu trưởng</label>
                    <input type="text" id="principal_name" name="principal_name" value="{{ old('principal_name', $settings['principal_name']->value ?? '') }}"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="school_year">Niên khóa</label>
                    <input type="text" id="school_year" name="school_year" value="{{ old('school_year', $settings['school_year']->value ?? '2025-2026') }}" required
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                </div>
            </div>
        </div>

        <!-- Cấu hình Tiết cố định (MỚI) -->
        <div class="content-card">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                    Tiết cố định & Ràng buộc
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase mb-1">Tiết Chào cờ</label>
                        <select name="chao_co_period" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm font-bold bg-slate-50">
                            <option value="1" {{ ($settings['chao_co_period']->value ?? 1) == 1 ? 'selected' : '' }}>Tiết 1 (Sáng)</option>
                            <option value="6" {{ ($settings['chao_co_period']->value ?? 1) == 6 ? 'selected' : '' }}>Tiết 1 (Chiều)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase mb-1">Tiết Sinh hoạt</label>
                        <select name="sinh_hoat_period" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm font-bold bg-slate-50">
                            <option value="5" {{ ($settings['sinh_hoat_period']->value ?? 5) == 5 ? 'selected' : '' }}>Tiết 5 (T7 Sáng)</option>
                            <option value="10" {{ ($settings['sinh_hoat_period']->value ?? 10) == 10 ? 'selected' : '' }}>Tiết 10 (T7 Chiều)</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-3 pt-2">
                    <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-slate-50 rounded-lg transition-colors border border-transparent hover:border-slate-100">
                        <input type="checkbox" name="check_teacher_conflict" value="1" class="w-5 h-5 text-blue-600 rounded" {{ ($settings['check_teacher_conflict']->value ?? 1) ? 'checked' : '' }}>
                        <span class="text-sm font-bold text-slate-700">Kiểm tra trùng giờ Giáo viên</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer p-2 hover:bg-slate-50 rounded-lg transition-colors border border-transparent hover:border-slate-100">
                        <input type="checkbox" name="check_room_conflict" value="1" class="w-5 h-5 text-blue-600 rounded" {{ ($settings['check_room_conflict']->value ?? 1) ? 'checked' : '' }}>
                        <span class="text-sm font-bold text-slate-700">Kiểm tra trùng Phòng thực hành</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Cấu hình Ca học -->
        <div class="content-card">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Thông số Thời gian
                </h2>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="days_start">Học từ Thứ</label>
                        <select id="days_start" name="days_start" required
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 text-sm font-bold">
                            @for($i = 2; $i <= 7; $i++)
                                <option value="{{ $i }}" {{ ($settings['days_start']->value ?? 2) == $i ? 'selected' : '' }}>Thứ {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="days_end">Đến Thứ</label>
                        <select id="days_end" name="days_end" required
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 text-sm font-bold">
                            @for($i = 2; $i <= 7; $i++)
                                <option value="{{ $i }}" {{ ($settings['days_end']->value ?? 7) == $i ? 'selected' : '' }}>Thứ {{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="periods_per_day">Số tiết / ngày</label>
                        <input type="number" id="periods_per_day" name="periods_per_day" value="{{ $settings['periods_per_day']->value ?? 10 }}" min="1" max="20" required
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 text-sm font-bold">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="lunch_after_period">Giao tiết S-C</label>
                        <input type="number" id="lunch_after_period" name="lunch_after_period" value="{{ $settings['lunch_after_period']->value ?? 5 }}" min="1" required
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 text-sm font-bold">
                    </div>
                </div>
            </div>
        </div>

        <!-- Thuật toán Xếp lịch -->
        <div class="content-card">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Giới hạn & Tối ưu
                </h2>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="max_consecutive_periods">Số tiết tối đa liên tiếp của 1 GV</label>
                    <input type="number" id="max_consecutive_periods" name="max_consecutive_periods" value="{{ $settings['max_consecutive_periods']->value ?? 4 }}" min="1" required
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 text-sm font-bold">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="max_gap_periods">Khoảng nghỉ tối đa giữa các tiết</label>
                    <input type="number" id="max_gap_periods" name="max_gap_periods" value="{{ $settings['max_gap_periods']->value ?? 2 }}" min="0" required
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 text-sm font-bold">
                </div>
            </div>
        </div>
    </div>

    <div class="mt-8 flex justify-end gap-3">
        <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
            Lưu Cấu hình Hệ thống
        </button>
    </div>
</form>
@endsection
