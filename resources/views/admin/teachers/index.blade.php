@extends('admin.layouts.app')

@section('title', 'Quản lý Giáo viên')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Giáo viên</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Danh sách tất cả giáo viên và phân công chuyên môn</p>
    </div>
    <a href="{{ route('admin.teachers.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold flex items-center gap-2 shadow-lg shadow-blue-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Thêm Giáo viên
    </a>
</div>

<div class="content-card">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <form action="{{ route('admin.teachers.index') }}" method="GET" class="w-full sm:max-w-md relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm tên, mã viết tắt..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors text-sm font-medium">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Giáo viên</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Mã tra cứu</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Định mức</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Còn lại</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($teachers as $teacher)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">{{ $teacher->name }}</div>
                        <div class="text-xs text-slate-500 font-medium mt-0.5">{{ $teacher->short_code ?? '---' }}</div>
                        @if($teacher->homeroomClass)
                            <span class="inline-block mt-1 text-[10px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded font-bold border border-indigo-100">GVCN: {{ $teacher->homeroomClass->name }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-md">{{ $teacher->lookup_code }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-bold text-slate-700">{{ $teacher->quota }}</span>
                        <span class="text-xs text-slate-500">tiết/tuần</span>
                    </td>
                    <td class="px-6 py-4">
                        @php $remaining = $teacher->remaining_quota; @endphp
                        <span class="text-sm font-extrabold {{ $remaining < 0 ? 'text-red-500' : 'text-emerald-500' }}">
                            {{ $remaining }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.teachers.edit', $teacher) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors tooltip" title="Chỉnh sửa">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa giáo viên này?');" class="inline-block">
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
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <p class="text-slate-500 font-medium">Chưa có dữ liệu giáo viên nào.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($teachers->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $teachers->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
