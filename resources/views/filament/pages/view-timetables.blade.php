<x-filament-panels::page>
    <div class="w-full"> {{-- Div Root duy nhất để tránh lỗi Multiple Root --}}
        
        <style>
            [x-cloak] { display: none !important; }
            
            /* Màu nút cố định - Ép kiểu để không bị màu trắng của Filament che */
            .force-btn-excel { background-color: #059669 !important; color: white !important; font-weight: 800 !important; }
            .force-btn-print { background-color: #2563eb !important; color: white !important; font-weight: 800 !important; }
            
            /* Định dạng bảng hiện đại trên web */
            .modern-grid { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #e2e8f0; border-radius: 1rem; overflow: hidden; }
            .modern-grid th { background: #f8fafc; padding: 12px; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
            .modern-grid td { padding: 12px; border-bottom: 1px solid #f1f5f9; border-right: 1px solid #f1f5f9; vertical-align: middle; background: white; }
        </style>

        <div class="no-print bg-white/80 backdrop-blur-md p-6 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8 flex justify-between items-center transition-all hover:shadow-md">
            <div class="flex items-center gap-6">
                <div class="flex flex-col">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2 mb-1">Chọn khối lớp:</span>
                    <div class="flex items-center gap-2 bg-slate-100 p-1.5 rounded-2xl flex gap-1 border border-slate-200">
                        @foreach(['10', '11', '12'] as $grade)
                            <button wire:click="$set('selectedGrade', '{{ $grade }}')" 
                                class="px-8 py-2.5 rounded-xl text-xs font-black transition-all duration-300 {{ $selectedGrade == $grade ? 'bg-white text-blue-600 shadow-md' : 'text-slate-500 hover:text-slate-800' }}">
                                KHỐI {{ $grade }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="text-right pr-4">
                <h1 class="text-xl font-black text-slate-800 tracking-tighter uppercase leading-none italic">THPT Nguyễn Bỉnh Khiêm</h1>
                <p class="text-[9px] text-blue-500 font-bold uppercase tracking-[0.3em] mt-1 italic">School Timetable System</p>
            </div>
        </div>

        <div class="space-y-6">
            @foreach($timetables as $index => $tkb)
                <div x-data="{ expanded: false }" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm transition-all duration-500 hover:shadow-xl overflow-hidden page-break">
                    
                    <div class="p-6 flex justify-between items-center cursor-pointer select-none bg-gradient-to-r from-slate-50/50 to-white" @click="expanded = !expanded">
                        <div class="flex items-center gap-6">
                            <div class="w-16 h-16 bg-blue-600 rounded-[1.5rem] shadow-lg shadow-blue-200 flex flex-col items-center justify-center text-white transition-transform duration-500" :class="expanded ? 'scale-110 rotate-3' : ''">
                                <span class="text-[9px] font-bold opacity-70 leading-none italic">LỚP</span>
                                <span class="font-black text-2xl leading-none mt-1 uppercase">{{ $tkb['name'] }}</span>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-800 tracking-tight leading-none mb-1 uppercase">TKB LỚP {{ $tkb['name'] }}</h3>
                                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">GVCN: <span class="text-blue-600 font-black italic">{{ $tkb['gvcn'] }}</span></p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 no-print" @click.stop>
                            <button type="button" wire:click="exportExcel({{ $tkb['id'] }})" class="force-btn-excel px-6 py-3 rounded-2xl text-[10px] uppercase shadow-lg shadow-emerald-100 transition-all flex items-center gap-2 border-none active:scale-95">
                                <x-heroicon-m-arrow-down-tray class="w-4 h-4"/> Lưu Excel
                            </button>
                            
                            <button type="button" onclick="printTable('area-{{ $index }}', '{{ $tkb['name'] }}', '{{ $tkb['gvcn'] }}')" class="force-btn-print px-6 py-3 rounded-2xl text-[10px] uppercase shadow-lg shadow-blue-100 transition-all flex items-center gap-2 border-none active:scale-95">
                                <x-heroicon-m-printer class="w-4 h-4"/> In Nhanh
                            </button>
                            
                            <div class="ml-4 transition-transform duration-500" :class="expanded ? 'rotate-180' : ''">
                                <x-heroicon-o-chevron-down class="w-6 h-6 text-slate-300" />
                            </div>
                        </div>
                    </div>

                    <div x-show="expanded" x-collapse x-cloak id="area-{{ $index }}" class="p-10 border-t border-slate-50 bg-white">
                        <div class="overflow-x-auto rounded-[2rem] border border-slate-200 shadow-inner p-1">
                            <table class="modern-grid text-center table-fixed">
                                <thead>
                                    <tr class="bg-slate-50/80">
                                        <th class="w-20 border-r border-slate-100">Tiết</th>
                                        @for($d=2; $d<=7; $d++) <th class="border-r border-slate-100">Thứ {{ $d }}</th> @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    @for($p=1; $p<=10; $p++)
                                        @if($p==6) 
                                            <tr><td colspan="7" class="py-2 text-[9px] font-black text-slate-300 uppercase tracking-[2em] border-b border-slate-100 italic bg-slate-50">Nghỉ trưa</td></tr> 
                                        @endif
                                        <tr>
                                            <td class="font-black text-slate-200 text-xl italic border-r border-slate-100 leading-none">{{ $p }}</td>
                                            @for($d=2; $d<=7; $d++)
                                                <td class="h-28 w-40 text-left relative transition-colors hover:bg-blue-50/40">
                                                    @if(isset($tkb['data'][$d][$p]))
                                                        <div class="font-black text-slate-800 text-[14px] leading-tight mb-1 uppercase tracking-tighter">{{ $tkb['data'][$d][$p]['sub'] }}</div>
                                                        <div class="text-[10px] text-blue-600 font-bold italic opacity-80">{{ $tkb['data'][$d][$p]['tea'] }}</div>
                                                    @else
                                                        <div class="w-full h-full bg-slate-50/50 rounded-xl"></div>
                                                    @endif
                                                </td>
                                            @endfor
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @script
        <script>
            // Hàm IN NHANH được giữ lại
            window.printTable = function(divId, className, gvcn) {
                const content = document.getElementById(divId).innerHTML;
                const win = window.open('', '_blank');
                win.document.write('<html><head><title>In TKB - ' + className + '</title>');
                win.document.write('<style>');
                win.document.write('body{font-family:sans-serif;padding:40px;text-align:center;}');
                win.document.write('table{width:100%;border-collapse:collapse;margin-top:20px;table-layout:fixed;}');
                win.document.write('td,th{border:1px solid #000;padding:12px;text-align:center;word-wrap:break-word;}');
                win.document.write('h1{text-transform:uppercase; font-size:26pt; margin:0;}');
                win.document.write('p{font-size:14pt; font-weight:bold; margin-top:10px;}');
                win.document.write('</style></head><body>');
                win.document.write('<h1>THỜI KHÓA BIỂU LỚP ' + className + '</h1>');
                win.document.write('<p>GVCN: ' + gvcn + '</p>');
                win.document.write('<p style="font-size:10pt; color:gray; text-transform:uppercase; letter-spacing:4px;">Trường THPT Nguyễn Bỉnh Khiêm</p>');
                win.document.write('<hr>' + content);
                win.document.write('</body></html>');
                win.document.close();
                win.focus();
                setTimeout(function() { win.print(); win.close(); }, 600);
            };
        </script>
        @endscript
    </div>
</x-filament-panels::page>