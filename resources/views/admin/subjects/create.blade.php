@extends('admin.layouts.app')

@section('title', 'Thêm Môn học mới')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Thêm Môn học</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Khởi tạo môn học mới và cấu hình các ràng buộc xếp lịch</p>
    </div>
    <a href="{{ route('admin.subjects.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Quay lại
    </a>
</div>

<div class="content-card p-6">
    <form action="{{ route('admin.subjects.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Thông tin Cơ bản -->
            <div class="space-y-5">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-2">Thông tin Cơ bản</h3>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="name">Tên môn học <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border @error('name') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors"
                        placeholder="VD: Toán học">
                    @error('name')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Loại môn học <span class="text-red-500">*</span></label>
                    <div class="flex gap-6 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="1" class="w-4 h-4 text-blue-600 focus:ring-blue-500 border-slate-300" {{ old('type', 1) == 1 ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-slate-700">Lý thuyết</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="type" value="2" class="w-4 h-4 text-purple-600 focus:ring-purple-500 border-slate-300" {{ old('type') == 2 ? 'checked' : '' }}>
                            <span class="text-sm font-medium text-slate-700">Thực hành</span>
                        </label>
                    </div>
                    @error('type')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="flex items-center gap-3 cursor-pointer p-3 bg-amber-50 rounded-xl border border-amber-100 hover:bg-amber-100/50 transition-colors">
                        <input type="checkbox" name="is_fixed" value="1" class="w-5 h-5 text-amber-600 rounded border-amber-300 focus:ring-amber-500" {{ old('is_fixed') ? 'checked' : '' }}>
                        <div>
                            <span class="text-sm font-bold text-amber-800 block">Tiết cố định (Ví dụ: Chào cờ, SHL)</span>
                            <span class="text-xs text-amber-600 mt-0.5 block">Sẽ được gắn thẳng vào TKB, không bị TKB tự động thay đổi.</span>
                        </div>
                    </label>
                    @error('is_fixed')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="room_category_id">Yêu cầu phòng học (Tùy chọn)</label>
                    <select id="room_category_id" name="room_category_id"
                        class="w-full px-3 py-2 border @error('room_category_id') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        <option value="">-- Không yêu cầu phòng chuyên dụng --</option>
                        @foreach($roomCategories as $cat)
                            <option value="{{ $cat->id }}" {{ old('room_category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-500 mt-1">Thường dùng cho các môn Thực hành cần gắn với các nhóm phòng cụ thể.</p>
                    @error('room_category_id')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <!-- Cấu hình Xếp lịch -->
            <div class="space-y-5">
                <h3 class="text-lg font-bold text-slate-800 border-b border-slate-100 pb-2">Ràng buộc Xếp lịch</h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="lessons_per_week">Số tiết / Tuần <span class="text-red-500">*</span></label>
                        <input type="number" id="lessons_per_week" name="lessons_per_week" value="{{ old('lessons_per_week', 2) }}" min="1" required
                            class="w-full px-3 py-2 border @error('lessons_per_week') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        <p class="text-[10px] text-slate-500 mt-1">Số tiết tiêu chuẩn dùng làm mặc định khi tạo mới Chương trình học.</p>
                        @error('lessons_per_week')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="max_lessons_per_day">Tối đa tiết / Ngày <span class="text-red-500">*</span></label>
                        <input type="number" id="max_lessons_per_day" name="max_lessons_per_day" value="{{ old('max_lessons_per_day', 2) }}" min="1" max="5" required
                            class="w-full px-3 py-2 border @error('max_lessons_per_day') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        @error('max_lessons_per_day')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="consecutive_periods">Số tiết liền mạch <span class="text-red-500">*</span></label>
                        <input type="number" id="consecutive_periods" name="consecutive_periods" value="{{ old('consecutive_periods', 1) }}" min="1" max="5" required
                            class="w-full px-3 py-2 border @error('consecutive_periods') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        <p class="text-[10px] text-slate-500 mt-1">Ví dụ: Điền "2" nếu môn này dạy 2 tiết liền nhau (Tiết đôi).</p>
                        @error('consecutive_periods')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1" for="max_periods_per_day">Tối đa tiết dạy/ngày/GV <span class="text-red-500">*</span></label>
                        <input type="number" id="max_periods_per_day" name="max_periods_per_day" value="{{ old('max_periods_per_day', 5) }}" min="1" max="10" required
                            class="w-full px-3 py-2 border @error('max_periods_per_day') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        <p class="text-[10px] text-slate-500 mt-1">Giới hạn số tiết tối đa 1 Giáo viên được phân công môn này trong 1 ngày.</p>
                        @error('max_periods_per_day')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.subjects.index') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Lưu Môn học
            </button>
        </div>
    </form>
</div>
@endsection
