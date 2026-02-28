<x-filament-panels::page>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .print-only { display: none; }
        
        /* Cấu hình bản IN */
        @media print {
            .no-print { display: none !important; }
            .print-only { display: block !important; text-align: center; margin-bottom: 20px; }
            .page-break { page-break-after: always; border: none !important; box-shadow: none !important; margin: 0 !important; }
            body { background: white !important; padding: 0 !important; }
            table { width: 100% !important; border-collapse: collapse !important; border: 2px solid black !important; }
            td, th { border: 1px solid black !important; padding: 10px !important; color: black !important; font-size: 14px !important; }
        }
    </style>

    <div class="no-print bg-white/70 backdrop-blur-md p-6 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-4">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest px-2">Bộ lọc hiển thị:</span>
            <div class="bg-slate-100 p-1.5 rounded-2xl flex gap-1 border border-slate-200">
                @foreach(['10', '11', '12'] as $grade)
                    <button wire:click="$set('selectedGrade', '{{ $grade }}')" 
                        class="px-8 py-2.5 rounded-xl text-xs font-black transition-all duration-300 {{ $selectedGrade == $grade ? 'bg-white text-blue-600 shadow-md' : 'text-slate-500 hover:text-slate-800' }}">
                        KHỐI {{ $grade }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="text-right pr-4">
            <h1 class="text-xl font-black text-slate-800 tracking-tighter uppercase leading-none italic">THPT Nguyễn Bỉnh Khiêm</h1>
            <p class="text-[9px] text-blue-500 font-bold uppercase tracking-[0.3em] mt-1 italic">Hệ thống quản lý thời khóa biểu</p>
        </div>
    </div>

    <div class="space-y-6">
        @foreach($timetables as $index => $tkb)
            <div x-data="{ isExpanded: false }" class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm transition-all duration-500 hover:shadow-xl overflow-hidden page-break">
                
                <div class="p-6 flex justify-between items-center cursor-pointer select-none bg-gradient-to-r from-slate-50/50 to-white" @click="isExpanded = !isExpanded">
                    <div class="flex items-center gap-6">
                        <div class="w-16 h-16 bg-blue-600 rounded-[1.5rem] shadow-lg shadow-blue-200 flex flex-col items-center justify-center text-white transition-transform duration-500" :class="isExpanded ? 'scale-110 rotate-3' : ''">
                            <span class="text-[9px] font-bold opacity-60 leading-none italic">LỚP</span>
                            <span class="font-black text-2xl leading-none mt-1 uppercase">{{ $tkb['name'] }}</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-800 tracking-tight leading-none mb-1 uppercase">Thời khóa biểu chi tiết lớp {{ $tkb['name'] }}</h3>
                            <p class="text-xs font-bold text-slate-400">GVCN: <span class="text-blue-500 font-black italic">{{ $tkb['gvcn'] }}</span></p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 no-print" @click.stop>
                        <button type="button" onclick="runExportPDF('area-{{ $index }}', '{{ $tkb['name'] }}')" class="bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase transition-all shadow-sm flex items-center gap-2 border border-emerald-100">
                            <x-heroicon-m-arrow-down-tray class="w-4 h-4"/> Lưu PDF
                        </button>
                        <button type="button" onclick="runPrint('area-{{ $index }}')" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white px-5 py-2.5 rounded-2xl text-[10px] font-black uppercase transition-all shadow-sm flex items-center gap-2 border border-blue-100">
                            <x-heroicon-m-printer class="w-4 h-4"/> In nhanh
                        </button>
                        <div class="ml-4 transition-transform duration-500" :class="isExpanded ? 'rotate-180' : ''">
                            <x-heroicon-o-chevron-down class="w-6 h-6 text-slate-300" />
                        </div>
                    </div>
                </div>

                <div x-show="isExpanded" x-collapse x-cloak id="area-{{ $index }}" class="p-10 border-t border-slate-50 bg-white">
                    
                    <div class="print-only text-center mb-10">
                        <div style="font-size: 34pt; font-weight: 900; color: black; text-transform: uppercase; line-height: 1;">THỜI KHÓA BIỂU LỚP {{ $tkb['name'] }}</div>
                        <div style="font-size: 16pt; font-weight: 800; color: #333; margin-top: 5px;">GIÁO VIÊN CHỦ NHIỆM: {{ $tkb['gvcn'] }}</div>
                        <div style="font-size: 11pt; color: #777; text-transform: uppercase; letter-spacing: 5px; margin-top: 10px; border-top: 3px solid #000; display: inline-block; padding-top: 5px;">Trường THPT Nguyễn Bỉnh Khiêm</div>
                    </div>

                    <div class="overflow-x-auto rounded-[2rem] border border-slate-200 shadow-inner p-1 bg-slate-50/20">
                        <table class="w-full border-collapse text-center table-fixed bg-white rounded-2xl overflow-hidden">
                            <thead>
                                <tr class="bg-slate-50/80">
                                    <th class="p-5 border-b border-r border-slate-200 w-20 text-[10px] font-black text-slate-400 uppercase">Tiết</th>
                                    @for($d=2; $d<=7; $d++) 
                                        <th class="p-5 border-b border-r border-slate-200 text-sm font-black text-slate-700 uppercase bg-blue-50/20">Thứ {{ $d }}</th> 
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                                @for($p=1; $p<=10; $p++)
                                    @if($p==6) 
                                        <tr class="bg-slate-100/50"><td colspan="7" class="py-2 text-[9px] font-black text-slate-300 uppercase tracking-[2em] border-b border-slate-100 italic bg-slate-50">Nghỉ trưa</td></tr> 
                                    @endif
                                    <tr>
                                        <td class="p-5 border-b border-r border-slate-100 font-black text-slate-200 text-xl italic leading-none">{{ $p }}</td>
                                        @for($d=2; $d<=7; $d++)
                                            <td class="p-3 border-b border-r border-slate-100 h-28 align-middle text-left relative transition-colors hover:bg-blue-50/40">
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

    <script>
        function runPrint(divId) {
            var content = document.getElementById(divId).innerHTML;
            var win = window.open('', '_blank', 'width=1200,height=800');
            win.document.write('<html><head><title>Bản In TKB</title>');
            win.document.write('<style>body{font-family:sans-serif;padding:40px;text-align:center;background:white;} table{width:100%;border-collapse:collapse;margin-top:20px;table-layout:fixed;} td,th{border:1.5px solid black;padding:12px;text-align:center;word-wrap:break-word;} .no-print{display:none;} .print-only{display:block !important;} h1{margin:0;} .hidden{display:none;}</style>');
            win.document.write('</head><body>');
            win.document.write(content);
            win.document.write('</body></html>');
            win.document.close();
            win.focus();
            setTimeout(function() {
                win.print();
                win.close();
            }, 600);
        }

        function runExportPDF(divId, className) {
            var element = document.getElementById(divId);
            var header = element.querySelector('.print-only');
            
            header.style.display = 'block'; // Hiện tiêu đề tạm thời

            var opt = {
                margin: 0.3,
                filename: 'TKB_Lop_' + className + '.pdf',
                image: { type: 'jpeg', quality: 1 },
                html2canvas: { scale: 3, useCORS: true, letterRendering: true },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
            };

            html2pdf().set(opt).from(element).save().then(function() {
                header.style.display = 'none'; // Ẩn lại sau khi xong
            });
        }
    </script>
</x-filament-panels::page>