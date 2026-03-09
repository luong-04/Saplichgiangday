@extends('admin.layouts.app')

@section('title', 'Thêm Chương trình học')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Thêm Chương trình học</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Phân bổ số tiết của môn học cho một khối cụ thể</p>
    </div>
    <a href="{{ route('admin.curricula.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Quay lại
    </a>
</div>

<div class="content-card p-6 max-w-3xl">
    <form action="{{ route('admin.curricula.store') }}" method="POST">
        @csrf

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="grade">Khối lớp <span class="text-red-500">*</span></label>
                    <select id="grade" name="grade" required
                        class="w-full px-3 py-2 border @error('grade') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        <option value="">-- Chọn khối --</option>
                        <option value="10" {{ old('grade') == '10' ? 'selected' : '' }}>Khối 10</option>
                        <option value="11" {{ old('grade') == '11' ? 'selected' : '' }}>Khối 11</option>
                        <option value="12" {{ old('grade') == '12' ? 'selected' : '' }}>Khối 12</option>
                    </select>
                    @error('grade')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="subject_id">Môn học <span class="text-red-500">*</span></label>
                    <select id="subject_id" name="subject_id" required
                        class="w-full px-3 py-2 border @error('subject_id') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors"
                        onchange="updateDefaultLessons(this)">
                        <option value="" data-lessons="">-- Chọn môn học --</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" data-lessons="{{ $subject->lessons_per_week }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }} (Chuẩn: {{ $subject->lessons_per_week }} tiết)
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1" for="lessons_per_week">Số tiết / Tuần <span class="text-red-500">*</span></label>
                <div class="relative w-1/2">
                    <input type="number" id="lessons_per_week" name="lessons_per_week" value="{{ old('lessons_per_week', 2) }}" min="1" required
                        class="w-full px-3 py-2 border @error('lessons_per_week') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors text-lg font-bold">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <span class="text-slate-400 font-medium">tiết</span>
                    </div>
                </div>
                <p class="text-xs text-slate-500 mt-2 p-3 bg-blue-50 text-blue-800 rounded-lg border border-blue-100 flex items-start gap-2">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Lưu ý: Bạn có thể nhập số tiết khác với chuẩn của môn học. Khi xếp thời khóa biểu cho Khối này, hệ thống sẽ ưu tiên số tiết được thiết lập tại đây thay vì số tiết tiêu chuẩn của toàn trường lập theo môn học.</span>
                </p>
                @error('lessons_per_week')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.curricula.index') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Lưu Chương trình
            </button>
        </div>
    </form>
</div>

<script>
    function updateDefaultLessons(selectElement) {
        const option = selectElement.options[selectElement.selectedIndex];
        const defaultLessons = option.getAttribute('data-lessons');
        if (defaultLessons) {
            document.getElementById('lessons_per_week').value = defaultLessons;
        }
    }
</script>
@endsection
