@extends('admin.layouts.app')

@section('title', 'Thêm Giáo viên mới')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Thêm Giáo viên</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Nhập thông tin định danh và phân công chuyên môn</p>
    </div>
    <a href="{{ route('admin.teachers.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Quay lại
    </a>
</div>

<div class="content-card p-6">
    <form action="{{ route('admin.teachers.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Thông tin định danh -->
            <div>
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-2 mb-4">Thông tin cơ bản</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="name">Họ tên <span class="text-red-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-3 py-2 border @error('name') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors"
                            placeholder="VD: Nguyễn Văn A">
                        @error('name')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1" for="short_code">Tên viết tắt</label>
                            <input type="text" id="short_code" name="short_code" value="{{ old('short_code') }}"
                                class="w-full px-3 py-2 border @error('short_code') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors"
                                placeholder="VD: N.V.A">
                            @error('short_code')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1" for="lookup_code">Mã tra cứu <span class="text-red-500">*</span></label>
                            <input type="text" id="lookup_code" name="lookup_code" value="{{ old('lookup_code') }}" required
                                class="w-full px-3 py-2 border @error('lookup_code') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors font-mono"
                                placeholder="VD: GV_NVA">
                            @error('lookup_code')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1" for="quota">Định mức tiết/tuần <span class="text-red-500">*</span></label>
                            <input type="number" id="quota" name="quota" value="{{ old('quota', 17) }}" min="0" required
                                class="w-full px-3 py-2 border @error('quota') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                            @error('quota')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1" for="max_periods_per_day">Tối đa tiết/ngày <span class="text-red-500">*</span></label>
                            <input type="number" id="max_periods_per_day" name="max_periods_per_day" value="{{ old('max_periods_per_day', 5) }}" min="1" max="10" required
                                class="w-full px-3 py-2 border @error('max_periods_per_day') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                            @error('max_periods_per_day')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="homeroom_class_id">Chủ nhiệm lớp</label>
                        <select id="homeroom_class_id" name="homeroom_class_id"
                            class="w-full px-3 py-2 border @error('homeroom_class_id') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                            <option value="">-- Không chủ nhiệm --</option>
                            @foreach ($homeroomClasses as $class)
                                <option value="{{ $class->id }}" {{ old('homeroom_class_id') == $class->id ? 'selected' : '' }}>
                                    Lớp {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('homeroom_class_id')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                        <p class="text-[11px] text-slate-500 mt-1">Chỉ hiển thị các lớp chưa có giáo viên chủ nhiệm.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Ca dạy</label>
                        <div class="flex gap-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="teaching_shifts[]" value="morning" class="w-4 h-4 text-blue-600 rounded" {{ is_array(old('teaching_shifts')) && in_array('morning', old('teaching_shifts')) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-slate-700">Sáng</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="teaching_shifts[]" value="afternoon" class="w-4 h-4 text-blue-600 rounded" {{ is_array(old('teaching_shifts')) && in_array('afternoon', old('teaching_shifts')) ? 'checked' : '' }}>
                                <span class="text-sm font-medium text-slate-700">Chiều</span>
                            </label>
                        </div>
                        @error('teaching_shifts')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <!-- Phân công giảng dạy -->
            <div>
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-2 mb-4">Phân công chuyên môn</h3>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Môn giảng dạy (Chọn nhiều) <span class="text-red-500">*</span></label>
                        <div class="bg-slate-50 p-3 rounded-lg border @error('subjects') border-red-500 @else border-slate-200 @enderror h-48 overflow-y-auto">
                            <div class="grid grid-cols-2 gap-2">
                                @forelse ($subjects as $subject)
                                    <label class="flex items-center gap-2 cursor-pointer p-1.5 hover:bg-slate-100 rounded transition-colors">
                                        <input type="checkbox" name="subjects[]" value="{{ $subject->id }}" class="w-4 h-4 text-blue-600 rounded" {{ is_array(old('subjects')) && in_array($subject->id, old('subjects')) ? 'checked' : '' }}>
                                        <span class="text-sm font-medium text-slate-700">{{ $subject->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-slate-500 col-span-2">Chưa có môn học nào trong hệ thống.</p>
                                @endforelse
                            </div>
                        </div>
                        @error('subjects')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Chỉ định Lớp giảng dạy (Tùy chọn)</label>
                        <p class="text-[11px] text-slate-500 mb-2 leading-relaxed">Khi xếp Ma trận TKB, ở các lớp được chọn, danh sách GV sẽ tự động ưu tiên hiển thị giáo viên này. Có thể chọn nhiều lớp.</p>
                        
                        <div class="bg-slate-50 p-3 rounded-lg border @error('assigned_classes') border-red-500 @else border-slate-200 @enderror h-60 overflow-y-auto">
                            <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                @forelse ($classes as $class)
                                    <label class="flex items-center gap-2 cursor-pointer p-1.5 hover:bg-slate-100 rounded transition-colors">
                                        <input type="checkbox" name="assigned_classes[]" value="{{ $class->id }}" class="w-4 h-4 text-emerald-600 rounded" {{ is_array(old('assigned_classes')) && in_array($class->id, old('assigned_classes')) ? 'checked' : '' }}>
                                        <span class="text-sm font-semibold text-slate-700">{{ $class->name }}</span>
                                    </label>
                                @empty
                                    <p class="text-sm text-slate-500 col-span-3">Chưa có lớp học nào trong hệ thống.</p>
                                @endforelse
                            </div>
                        </div>
                        @error('assigned_classes')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.teachers.index') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Lưu Giáo viên
            </button>
        </div>
    </form>
</div>
@endsection
