@extends('admin.layouts.app')

@section('title', 'Thêm Tiết Cố định')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Thêm Tiết Cố định</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Gắn cứng môn học (như Chào cờ, SHL) vào một khoảng thời gian cụ thể</p>
    </div>
    <a href="{{ route('admin.fixed-periods.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Quay lại
    </a>
</div>

<div class="content-card p-6 max-w-2xl">
    <form action="{{ route('admin.fixed-periods.store') }}" method="POST">
        @csrf

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1" for="subject_name">Tên môn cố định <span class="text-red-500">*</span></label>
                <input type="text" id="subject_name" name="subject_name" value="{{ old('subject_name') }}" required
                    class="w-full px-3 py-2 border @error('subject_name') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors"
                    placeholder="VD: Chào cờ, Sinh hoạt lớp, HĐTN...">
                @error('subject_name')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="day">Thứ <span class="text-red-500">*</span></label>
                    <select id="day" name="day" required
                        class="w-full px-3 py-2 border @error('day') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        @for($i = 2; $i <= 7; $i++)
                            <option value="{{ $i }}" {{ old('day') == $i ? 'selected' : '' }}>Thứ {{ $i }}</option>
                        @endfor
                    </select>
                    @error('day')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="periods">Tiết (Chọn nhiều) <span class="text-red-500">*</span></label>
                    <select id="periods" name="periods[]" multiple required
                        class="w-full px-3 py-2 border @error('periods') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors h-32">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ (is_array(old('periods')) && in_array($i, old('periods'))) ? 'selected' : '' }}>Tiết {{ $i }}</option>
                        @endfor
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1 italic">Giữ phím Ctrl (hoặc Cmd) để chọn nhiều tiết</p>
                    @error('periods')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="shift">Ca học <span class="text-red-500">*</span></label>
                    <select id="shift" name="shift" required
                        class="w-full px-3 py-2 border @error('shift') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        <option value="morning" {{ old('shift') == 'morning' ? 'selected' : '' }}>Sáng (Chính khóa)</option>
                        <option value="afternoon" {{ old('shift') == 'afternoon' ? 'selected' : '' }}>Chiều</option>
                    </select>
                    @error('shift')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-end">
                    <label class="flex items-center gap-3 cursor-pointer mb-2">
                        <input type="checkbox" name="auto_assign_homeroom" value="1" class="w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500" {{ old('auto_assign_homeroom') ? 'checked' : '' }}>
                        <span class="text-sm font-bold text-slate-700">Tự động gán GVCN</span>
                    </label>
                </div>
            </div>
            
            <p class="text-xs text-slate-500 p-3 bg-blue-50 text-blue-800 rounded-lg border border-blue-100 flex items-start gap-2">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Nếu chọn "Tự động gán GVCN" (như môn Sinh hoạt lớp), hệ thống sẽ tự tìm Giáo viên chủ nhiệm của lớp đó để gán vào TKB.</span>
            </p>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.fixed-periods.index') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Lưu Tiết Cố định
            </button>
        </div>
    </form>
</div>
@endsection
