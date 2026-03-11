@extends('admin.layouts.app')

@section('title', 'Quản lý Tiết Cố định')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
    <div>
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Tiết Cố định</h1>
        <p class="text-sm text-slate-500 font-medium mt-1">Cấu hình các tiết học mặc định không thay đổi (Chào cờ, Sinh hoạt lớp...)</p>
    </div>
    <a href="{{ route('admin.fixed-periods.create') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold flex items-center gap-2 shadow-lg shadow-blue-200 transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Thêm Tiết Cố định
    </a>
</div>

<div class="content-card">
    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <form action="{{ route('admin.fixed-periods.index') }}" method="GET" class="w-full relative flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1 sm:max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm tên môn (Chào cờ, SHL)..." class="w-full pl-10 pr-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white transition-colors text-sm font-medium">
            </div>
            <select name="shift" class="px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 bg-slate-50 focus:bg-white text-sm font-medium w-full sm:w-auto" onchange="this.form.submit()">
                <option value="">Tất cả Ca học</option>
                <option value="morning" {{ request('shift') == 'morning' ? 'selected' : '' }}>Buổi sáng</option>
                <option value="afternoon" {{ request('shift') == 'afternoon' ? 'selected' : '' }}>Buổi chiều</option>
            </select>
            @if(request('search') || request('shift'))
                <a href="{{ route('admin.fixed-periods.index') }}" class="px-4 py-2 text-slate-500 hover:text-slate-800 text-sm font-medium flex items-center gap-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg> Xóa lọc
                </a>
            @endif
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wider">
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Tên môn</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Thứ / Tiết</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Ca học</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200">Gán GVCN</th>
                    <th class="px-6 py-4 font-bold border-b border-slate-200 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($fixedPeriods as $fp)
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="font-bold text-slate-800">{{ $fp->subject_name }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded border border-slate-200">
                            Thứ {{ $fp->day }} - Tiết {{ $fp->period }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($fp->shift == 'morning')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 text-xs font-bold border border-amber-100">Sáng</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-100">Chiều</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($fp->auto_assign_homeroom)
                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded border border-emerald-100">Có tự động gán</span>
                        @else
                            <span class="text-xs text-slate-400">Không</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.fixed-periods.edit', $fp) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors tooltip" title="Chỉnh sửa">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.fixed-periods.destroy', $fp) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tiết cố định này?');" class="inline-block">
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
                        <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-slate-500 font-medium">Chưa có dữ liệu tiết cố định nào.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($fixedPeriods->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $fixedPeriods->links('pagination::tailwind') }}
    </div>
    @endif
</div>
@endsection
