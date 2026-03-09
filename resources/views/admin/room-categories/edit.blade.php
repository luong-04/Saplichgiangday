@extends('admin.layouts.app')

@section('title', 'Cập nhật Nhóm phòng')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Cập nhật Nhóm phòng</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Chỉnh sửa thông tin phân loại phòng học</p>
    </div>
    <a href="{{ route('admin.room-categories.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Quay lại
    </a>
</div>

<div class="content-card p-6 max-w-2xl">
    <form action="{{ route('admin.room-categories.update', $roomCategory) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1" for="name">Tên nhóm phòng <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $roomCategory->name) }}" required
                    class="w-full px-3 py-2 border @error('name') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                @error('name')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1" for="description">Mô tả (Tùy chọn)</label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 border @error('description') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">{{ old('description', $roomCategory->description) }}</textarea>
                @error('description')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.room-categories.index') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Cập nhật Nhóm phòng
            </button>
        </div>
    </form>
</div>
@endsection
