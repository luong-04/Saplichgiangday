@extends('admin.layouts.app')

@section('title', 'Cập nhật Phòng học')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Cập nhật Phòng học</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Chỉnh sửa thông tin phòng học và nhóm phòng phân loại</p>
    </div>
    <a href="{{ route('admin.rooms.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Quay lại
    </a>
</div>

<div class="content-card p-6 max-w-2xl">
    <form action="{{ route('admin.rooms.update', $room) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1" for="name">Tên phòng học <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $room->name) }}" required
                    class="w-full px-3 py-2 border @error('name') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                @error('name')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="capacity">Sức chứa (Chỗ ngồi) <span class="text-red-500">*</span></label>
                    <input type="number" id="capacity" name="capacity" value="{{ old('capacity', $room->capacity) }}" min="1" required
                        class="w-full px-3 py-2 border @error('capacity') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                    @error('capacity')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="room_category_id">Nhóm phòng học (Tùy chọn) </label>
                    <select id="room_category_id" name="room_category_id"
                        class="w-full px-3 py-2 border @error('room_category_id') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        <option value="">-- Phòng học thông thường --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('room_category_id', $room->room_category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('room_category_id')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>
            
            <p class="text-xs text-slate-500 p-3 bg-blue-50 text-blue-800 rounded-lg border border-blue-100 flex items-start gap-2">
                <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Nếu là phòng thực hành/chức năng (tin học, hóa, lý...), vui lòng chọn Nhóm phòng học tương ứng để hệ thống tự động xếp lịch khi môn học yêu cầu.</span>
            </p>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.rooms.index') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Cập nhật Phòng học
            </button>
        </div>
    </form>
</div>
@endsection
