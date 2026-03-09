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
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="school_name">Tên trường học <span class="text-red-500">*</span></label>
                    <input type="text" id="school_name" name="school_name" value="{{ old('school_name', isset($settings['school_name']) ? $settings['school_name']->value : 'Trường THPT Mẫu') }}" required
                        class="w-full px-3 py-2 border @error('school_name') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                    @error('school_name')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="school_year">Năm học</label>
                    <input type="text" id="school_year" name="school_year" value="{{ old('school_year', isset($settings['school_year']) ? $settings['school_year']->value : '2023 - 2024') }}" required
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                    <p class="text-xs text-slate-500 mt-1">VD: 2023 - 2024</p>
                </div>
            </div>
        </div>

        <!-- Cấu hình Ca học -->
        <div class="content-card">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Cấu hình Lịch học
                </h2>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="days_start">Học từ Thứ <span class="text-red-500">*</span></label>
                        <select id="days_start" name="days_start" required
                            class="w-full px-3 py-2 border @error('days_start') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                            @for($i = 2; $i <= 7; $i++)
                                <option value="{{ $i }}" {{ old('days_start', isset($settings['days_start']) ? $settings['days_start']->value : 2) == $i ? 'selected' : '' }}>Thứ {{ $i }}</option>
                            @endfor
                        </select>
                        @error('days_start')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="days_end">Đến Thứ <span class="text-red-500">*</span></label>
                        <select id="days_end" name="days_end" required
                            class="w-full px-3 py-2 border @error('days_end') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                            @for($i = 2; $i <= 7; $i++)
                                <option value="{{ $i }}" {{ old('days_end', isset($settings['days_end']) ? $settings['days_end']->value : 7) == $i ? 'selected' : '' }}>Thứ {{ $i }}</option>
                            @endfor
                        </select>
                        @error('days_end')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="periods_per_day">Tổng số tiết / ngày <span class="text-red-500">*</span></label>
                        <input type="number" id="periods_per_day" name="periods_per_day" value="{{ old('periods_per_day', isset($settings['periods_per_day']) ? $settings['periods_per_day']->value : 10) }}" min="1" max="20" required
                            class="w-full px-3 py-2 border @error('periods_per_day') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        @error('periods_per_day')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="lunch_after_period">Nghỉ trưa sau tiết <span class="text-red-500">*</span></label>
                        <input type="number" id="lunch_after_period" name="lunch_after_period" value="{{ old('lunch_after_period', isset($settings['lunch_after_period']) ? $settings['lunch_after_period']->value : 5) }}" min="1" required
                            class="w-full px-3 py-2 border @error('lunch_after_period') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        @error('lunch_after_period')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Thuật toán Xếp lịch -->
        <div class="content-card lg:col-span-2">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 rounded-t-xl">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Thuật toán & Ràng buộc Xếp lịch
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="max_consecutive_periods">Số tiết tối đa liên tiếp của 1 GV <span class="text-red-500">*</span></label>
                        <input type="number" id="max_consecutive_periods" name="max_consecutive_periods" value="{{ old('max_consecutive_periods', isset($settings['max_consecutive_periods']) ? $settings['max_consecutive_periods']->value : 4) }}" min="1" required
                            class="w-full px-3 py-2 border @error('max_consecutive_periods') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        <p class="text-[11px] text-slate-500 mt-1">Hệ thống sẽ không xếp giáo viên dạy liên tục vượt quá số tiết này.</p>
                        @error('max_consecutive_periods')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="max_gap_periods">Khoảng nghỉ tối đa giữa các tiết <span class="text-red-500">*</span></label>
                        <input type="number" id="max_gap_periods" name="max_gap_periods" value="{{ old('max_gap_periods', isset($settings['max_gap_periods']) ? $settings['max_gap_periods']->value : 2) }}" min="0" required
                            class="w-full px-3 py-2 border @error('max_gap_periods') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        <p class="text-[11px] text-slate-500 mt-1">Số tiết trống tối đa trong một buổi học của Giáo viên. Nhập 0 để xếp liền mạch.</p>
                        @error('max_gap_periods')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="space-y-5 flex flex-col justify-start">
                    <label class="flex items-start gap-3 cursor-pointer p-4 border border-blue-100 bg-blue-50/50 rounded-xl hover:bg-blue-50 transition-colors">
                        <div class="flex items-center h-5 mt-0.5">
                            <input type="hidden" name="enforce_room_assignment" value="0">
                            <input type="checkbox" id="enforce_room_assignment" name="enforce_room_assignment" value="1" class="w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500" 
                                {{ old('enforce_room_assignment', isset($settings['enforce_room_assignment']) ? $settings['enforce_room_assignment']->value : 1) ? 'checked' : '' }}>
                        </div>
                        <div>
                            <span class="text-sm font-bold text-blue-900 block mb-1">Bắt buộc xếp Phòng học theo Môn (Phòng bộ môn)</span>
                            <span class="text-xs text-blue-700 block leading-relaxed">Nếu bật, khi xếp lịch một môn có yêu cầu phòng thực hành (như Tin học, Lý...), hệ thống sẽ bắt buộc phải tìm được phòng trống thuộc nhóm phòng đó. Nếu Tắt, hệ thống sẽ bỏ qua để dễ xếp lịch hơn nếu thiếu phòng.</span>
                        </div>
                    </label>
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
