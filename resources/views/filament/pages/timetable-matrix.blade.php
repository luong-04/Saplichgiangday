<x-filament-panels::page>
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        <div class="w-full lg:w-[28%] bg-white p-6 rounded-2xl shadow-md border border-gray-100 sticky top-6">
            <h3 class="font-bold text-lg mb-4 text-gray-800 flex items-center gap-2">
                <span class="bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-sm">1</span> 
                Chọn Lớp Xếp Lịch
            </h3>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-600 mb-2">Khối</label>
                <select wire:model.live="selectedGrade" class="w-full border-gray-300 rounded-xl shadow-sm p-3 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    <option value="">-- Chọn Khối --</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade }}">{{ $grade }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-600 mb-2">Lớp học</label>
                <select wire:model.live="selectedClass" class="w-full border-gray-300 rounded-xl shadow-sm p-3 border focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" {{ !$selectedGrade ? 'disabled' : '' }}>
                    <option value="">-- Chọn Lớp --</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}">{{ $cls->name }}</option>
                    @endforeach
                </select>
            </div>

            <hr class="mb-6 border-gray-200">

            <h3 class="font-bold text-lg mb-4 text-gray-800 flex items-center gap-2">
                <span class="bg-blue-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-sm">2</span> 
                Tạo Thẻ Môn Học
            </h3>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-600 mb-2">Giáo viên phụ trách</label>
                <select wire:model.live="dragTeacherId" class="w-full border-gray-300 rounded-xl shadow-sm p-3 border focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">-- Chọn Giáo viên --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-600 mb-2">Môn học</label>
                <select wire:model.live="dragSubjectId" class="w-full border-gray-300 rounded-xl shadow-sm p-3 border focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">-- Chọn Môn --</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            @if($dragTeacherId && $dragSubjectId)
                @php
                    // Xử lý an toàn dữ liệu Livewire để không bị lỗi mảng
                    $teacherData = collect($teachers)->firstWhere('id', (int)$dragTeacherId);
                    $subjectData = collect($subjects)->firstWhere('id', (int)$dragSubjectId);
                    
                    $tName = $teacherData ? (is_array($teacherData) ? $teacherData['name'] : $teacherData->name) : 'Giáo viên';
                    $sName = $subjectData ? (is_array($subjectData) ? $subjectData['name'] : $subjectData->name) : 'Môn học';
                @endphp
                <div 
                    draggable="true" 
                    ondragstart="event.dataTransfer.setData('text/plain', JSON.stringify({ teacher_id: {{ $dragTeacherId }}, subject_id: {{ $dragSubjectId }} }))"
                    class="p-5 rounded-xl shadow-lg cursor-grab active:cursor-grabbing text-center hover:scale-[1.03] transition-transform duration-200"
                    style="background: linear-gradient(to bottom right, #2563eb, #1e40af); color: #ffffff; border: 1px solid #93c5fd;"
                >
                    <div style="color: #bfdbfe; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; margin-bottom: 0.5rem;">Cầm & Kéo thẻ này</div>
                    <div style="font-weight: 700; font-size: 1.25rem; line-height: 1.2; margin-bottom: 0.25rem;">{{ $sName }}</div>
                    <div style="font-size: 0.875rem; font-weight: 500; background-color: rgba(30, 58, 138, 0.5); padding: 0.25rem 0.6rem; border-radius: 0.5rem; display: inline-block;">{{ $tName }}</div>
                </div>
            @else
                <div class="bg-gray-50 p-6 rounded-xl border-2 border-dashed border-gray-300 text-center text-gray-400 text-sm font-medium">
                    Chọn Giáo viên & Môn học <br>để tạo thẻ xếp lịch
                </div>
            @endif
        </div>

        <div class="w-full lg:w-[72%] bg-white p-6 rounded-2xl shadow-md border border-gray-100 overflow-hidden">
            @if($selectedClass)
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-800">
                        Thời khóa biểu lớp: <span class="text-blue-600">{{ collect($classes)->firstWhere('id', $selectedClass)?->name }}</span>
                    </h2>
                </div>

                <div class="overflow-x-auto pb-4">
                    <table class="w-full text-center border-collapse border border-gray-200 min-w-[900px]">
                        <thead>
                            <tr class="bg-blue-50 text-blue-900 border-b-2 border-blue-200">
                                <th class="border-r border-gray-200 p-3 w-[10%] font-bold uppercase text-sm">Tiết</th>
                                @for($day = 2; $day <= 7; $day++)
                                    <th class="border-r border-gray-200 p-3 w-[15%] font-bold uppercase text-sm">Thứ {{ $day }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for($period = 1; $period <= 10; $period++)
                                @if($period == 6)
                                    <tr>
                                        <td colspan="7" class="border border-gray-200 bg-gray-100 text-gray-500 font-bold py-3 tracking-[0.3em] shadow-inner text-sm">
                                            NGHỈ TRƯA
                                        </td>
                                    </tr>
                                @endif

                                <tr class="border-b border-gray-200 hover:bg-gray-50 transition">
                                    <td class="border-r border-gray-200 p-2 bg-gray-50/50">
                                        <div class="font-bold text-lg text-gray-700">{{ $period }}</div>
                                        <div class="text-[11px] font-medium text-gray-400 uppercase tracking-wider">{{ $period <= 5 ? 'Sáng' : 'Chiều' }}</div>
                                    </td>
                                    
                                    @for($day = 2; $day <= 7; $day++)
                                        <td class="border-r border-gray-200 p-1.5 h-28 align-top bg-white relative group/cell">
                                            @if(isset($matrix[$day][$period]))
                                                <div class="bg-blue-50 border-l-4 border-blue-500 text-left p-3 rounded-r-lg shadow-sm h-full flex flex-col justify-start relative group hover:bg-blue-100 transition duration-200">
                                                    
                                                    <button wire:click="deleteSchedule({{ $matrix[$day][$period]['id'] }})" class="absolute top-1 right-1 bg-white text-red-500 hover:bg-red-500 hover:text-white rounded-md w-6 h-6 flex items-center justify-center text-lg font-bold shadow-sm opacity-0 group-hover:opacity-100 transition z-10" title="Xóa tiết này">
                                                        &times;
                                                    </button>
                                                    
                                                    <div class="font-bold text-sm text-gray-800 leading-tight break-words pr-4">
                                                        {{ $matrix[$day][$period]['subject'] }}
                                                    </div>
                                                    <div class="text-xs font-semibold text-blue-600 mt-1.5 break-words">
                                                        {{ $matrix[$day][$period]['teacher'] }}
                                                    </div>
                                                </div>
                                            @else
                                                <div 
                                                    ondragover="event.preventDefault(); this.classList.add('bg-blue-50', 'border-blue-400', 'scale-[0.98]');" 
                                                    ondragleave="this.classList.remove('bg-blue-50', 'border-blue-400', 'scale-[0.98]');"
                                                    ondrop="
                                                        event.preventDefault();
                                                        this.classList.remove('bg-blue-50', 'border-blue-400', 'scale-[0.98]');
                                                        let data = JSON.parse(event.dataTransfer.getData('text/plain'));
                                                        @this.assignSchedule({{ $day }}, {{ $period }}, data.teacher_id, data.subject_id);
                                                    "
                                                    class="h-full w-full flex flex-col items-center justify-center border-2 border-dashed border-gray-200 hover:border-blue-400 hover:bg-blue-50 rounded-lg cursor-pointer text-gray-400 transition-all duration-200"
                                                >
                                                    <svg class="w-5 h-5 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                    <span class="text-[11px] font-medium tracking-wide">Kéo thả</span>
                                                </div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-96 text-gray-400 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50/50">
                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-lg font-medium text-gray-500">Bảng thời khóa biểu đang trống</p>
                    <p class="text-sm mt-1">Vui lòng chọn Khối và Lớp ở cột bên trái để bắt đầu xếp lịch.</p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>