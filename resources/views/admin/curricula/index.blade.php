@extends('admin.layouts.app')

@section('title', 'Quản lý Chương trình học')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Chương trình học</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Phân bổ số tiết môn học theo từng khối lớp</p>
    </div>
    <a href="{{ route('admin.curricula.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold flex items-center gap-2 shadow-lg shadow-blue-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Thêm Chương trình
    </a>
</div>

<div class="content-card">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <form action="{{ route('admin.curricula.index') }}" method="GET" class="w-full relative flex flex-col sm:flex-row gap-3">
            <select name="grade" class="px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white text-sm font-medium w-full sm:w-auto" onchange="this.form.submit()">
                <option value="">Tất cả khối</option>
                <option value="10" {{ request('grade') == '10' ? 'selected' : '' }}>Khối 10</option>
                <option value="11" {{ request('grade') == '11' ? 'selected' : '' }}>Khối 11</option>
                <option value="12" {{ request('grade') == '12' ? 'selected' : '' }}>Khối 12</option>
            </select>
            <select name="subject_id" class="px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white text-sm font-medium w-full sm:w-auto" onchange="this.form.submit()">
                <option value="">Tất cả môn học</option>
                @foreach ($subjects as $sub)
                    <option value="{{ $sub->id }}" {{ request('subject_id') == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                @endforeach
            </select>
            @if(request('grade') || request('subject_id'))
                <a href="{{ route('admin.curricula.index') }}" class="px-4 py-2 text-slate-500 hover:text-slate-800 text-sm font-medium flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Xóa lọc
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Khối lớp</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Môn học</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Loại môn</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Số tiết / Tuần</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($curricula as $curriculum)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-extrabold text-xs">
                            {{ $curriculum->grade }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800 flex items-center gap-2">
                            {{ $curriculum->subject->name }}
                            @if($curriculum->subject->is_fixed)
                                <span class="bg-amber-100 text-amber-700 text-[10px] px-2 py-0.5 rounded uppercase font-extrabold tracking-wider">Cố định</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($curriculum->subject->type == 1)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100">Lý thuyết</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-purple-50 text-purple-700 text-xs font-bold border border-purple-100">Thực hành</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-extrabold text-slate-800">{{ $curriculum->lessons_per_week }}</span>
                            @if($curriculum->lessons_per_week != $curriculum->subject->lessons_per_week)
                                <span class="text-[10px] text-orange-500 font-medium bg-orange-50 px-1.5 py-0.5 rounded border border-orange-100">(Lệch chuẩn: {{ $curriculum->subject->lessons_per_week }})</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.curricula.edit', $curriculum) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors tooltip" title="Chỉnh sửa">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.curricula.destroy', $curriculum) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa hệ đào tạo này?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors tooltip" title="Xóa">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        <p class="text-slate-500 font-medium">Chưa có dữ liệu chương trình học nào.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($curricula->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $curricula->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
