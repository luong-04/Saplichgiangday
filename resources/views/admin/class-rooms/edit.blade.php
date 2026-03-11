@extends('admin.layouts.app')

@section('title', 'Cập nhật Lớp học')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Cập nhật Lớp học</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Chỉnh sửa thông tin cấp độ và phòng học mặc định</p>
    </div>
    <a href="{{ route('admin.class-rooms.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-bold flex items-center gap-2 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Quay lại
    </a>
</div>

<div class="content-card p-6 max-w-3xl">
    <form action="{{ route('admin.class-rooms.update', $classRoom) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1" for="name">Tên lớp học <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $classRoom->name) }}" required
                    class="w-full px-3 py-2 border @error('name') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                @error('name')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="grade">Khối lớp <span class="text-red-500">*</span></label>
                    <select id="grade" name="grade" required
                        class="w-full px-3 py-2 border @error('grade') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        <option value="">-- Chọn khối --</option>
                        <option value="10" {{ old('grade', $classRoom->grade) == 10 ? 'selected' : '' }}>Khối 10</option>
                        <option value="11" {{ old('grade', $classRoom->grade) == 11 ? 'selected' : '' }}>Khối 11</option>
                        <option value="12" {{ old('grade', $classRoom->grade) == 12 ? 'selected' : '' }}>Khối 12</option>
                    </select>
                    @error('grade')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1" for="shift">Ca học mặc định <span class="text-red-500">*</span></label>
                    <select id="shift" name="shift" required
                        class="w-full px-3 py-2 border @error('shift') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                        <option value="morning" {{ old('shift', $classRoom->shift) == 'morning' ? 'selected' : '' }}>Sáng (Chính khóa)</option>
                        <option value="afternoon" {{ old('shift', $classRoom->shift) == 'afternoon' ? 'selected' : '' }}>Chiều</option>
                    </select>
                    @error('shift')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1" for="default_room_id">Phòng học cố định (Tùy chọn)</label>
                <select id="default_room_id" name="default_room_id"
                    class="w-full px-3 py-2 border @error('default_room_id') border-red-500 @else border-slate-200 @enderror rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors">
                    <option value="">-- Không xếp phòng cố định --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}" {{ old('default_room_id', $classRoom->default_room_id) == $room->id ? 'selected' : '' }}>
                            Phòng {{ $room->name }} (Sức chứa: {{ $room->capacity }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-slate-500 mt-1">Lớp học sẽ mặc định được gắn vào phòng này nếu môn học không yêu cầu phòng chức năng đặc biệt.</p>
                @error('default_room_id')<p class="text-xs text-red-500 mt-1 font-medium">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
            <a href="{{ route('admin.class-rooms.index') }}" class="px-6 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-lg text-sm font-bold hover:bg-slate-50 transition-colors">
                Hủy bỏ
            </a>
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-200 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Cập nhật Lớp học
            </button>
        </div>
    </form>
</div>
@endsection
