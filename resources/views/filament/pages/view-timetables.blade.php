<x-filament-panels::page>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <div class="no-print bg-white p-6 rounded-2xl shadow-sm border mb-6 flex justify-between items-center">
        <div class="flex items-center gap-4">
            <span class="font-bold text-gray-600 italic">Chọn khối:</span>
            <select wire:model.live="selectedGrade" class="rounded-xl border-gray-200 font-bold text-blue-600">
                <option value="10">Khối 10</option>
                <option value="11">Khối 11</option>
                <option value="12">Khối 12</option>
            </select>
        </div>
        <div class="text-xs text-gray-400 uppercase tracking-widest font-bold">THPT Nguyễn Bỉnh Khiêm</div>
    </div>

    <div class="space-y-6">
        @foreach($timetables as $index => $tkb)
            <div x-data="{ isExpanded: true }" class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden page-break">
                
                <div class="p-4 flex justify-between items-center bg-slate-50 cursor-pointer hover:bg-slate-100 transition" @click="isExpanded = !isExpanded">
                    <div class="flex items-center gap-4">
                        <span class="bg-blue-600 text-white px-5 py-1.5 rounded-xl font-black uppercase shadow-md">LỚP {{ $tkb['name'] }}</span>
                        <span class="text-xs text-gray-400 italic no-print">(Bấm để thu gọn/mở rộng)</span>
                    </div>
                    
                    <div class="flex items-center gap-3 no-print" @click.stop>
                        <button onclick="downloadPDF('print-area-{{ $index }}', '{{ $tkb['name'] }}')" class="bg-emerald-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-emerald-700 shadow-md flex items-center gap-2 transition">
                             <x-heroicon-m-arrow-down-tray class="w-4 h-4"/> Tải PDF
                        </button>
                        <button onclick="printLop('print-area-{{ $index }}')" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 shadow-md flex items-center gap-2 transition">
                            <x-heroicon-m-printer class="w-4 h-4"/> In TKB
                        </button>
                    </div>
                </div>

                <div x-show="isExpanded" x-collapse id="print-area-{{ $index }}" class="p-10">
                    
                    <div class="text-center mb-10 hidden print-header">
                        <h1 style="font-size: 32pt; font-weight: 900; color: black; margin-bottom: 5px; text-transform: uppercase;">THỜI KHÓA BIỂU LỚP {{ $tkb['name'] }}</h1>
                        <p style="font-size: 16pt; font-weight: bold; color: #444; letter-spacing: 2px;">TRƯỜNG THPT NGUYỄN BỈNH KHIÊM</p>
                        <hr style="border: 2px solid black; width: 200px; margin: 20px auto;">
                    </div>

                    <table class="w-full border-collapse border-[3px] border-black text-center">
                        <thead>
                            <tr class="bg-slate-100">
                                <th class="border-[2px] border-black p-3 font-bold w-20 text-lg">Tiết</th>
                                @for($d=2; $d<=7; $d++) 
                                    <th class="border-[2px] border-black p-3 font-bold uppercase text-lg">Thứ {{ $d }}</th> 
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for($p=1; $p<=10; $p++)
                                @if($p==6) <tr><td colspan="7" class="border-[2px] border-black font-bold py-1 bg-gray-50 text-xs uppercase tracking-widest">--- Nghỉ trưa ---</td></tr> @endif
                                <tr>
                                    <td class="border-[2px] border-black p-4 font-black bg-slate-50 text-gray-500 text-xl">{{ $p }}</td>
                                    @for($d=2; $d<=7; $d++)
                                        <td class="border-[2px] border-black p-2 h-24 w-40 align-middle text-left relative">
                                            @if(isset($tkb['data'][$d][$p]))
                                                <div class="font-black text-gray-950 text-[14px] leading-tight mb-1 uppercase">{{ $tkb['data'][$d][$p]['sub'] }}</div>
                                                <div class="text-[11px] text-blue-700 font-bold italic">{{ $tkb['data'][$d][$p]['tea'] }}</div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function printLop(divId) {
            const content = document.getElementById(divId).innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write('<html><head><title>In Thời Khóa Biểu</title>');
            printWindow.document.write('<style>body{font-family:sans-serif;padding:40px;} table{width:100%;border-collapse:collapse;} td,th{border:2px solid black;padding:12px;text-align:center;} .no-print{display:none;} .print-header{display:block !important;} h1{text-align:center;} p{text-align:center;}</style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write(content);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 500);
        }

        function downloadPDF(divId, className) {
            const element = document.getElementById(divId);
            // Hiện tiêu đề lớp trước khi chụp ảnh PDF
            const header = element.querySelector('.print-header');
            header.style.display = 'block';
            
            const opt = {
                margin: 0.3,
                filename: 'TKB_Lop_' + className + '.pdf',
                image: { type: 'jpeg', quality: 1 },
                html2canvas: { scale: 3, useCORS: true },
                jsPDF: { unit: 'in', format: 'a4', orientation: 'landscape' }
            };
            
            html2pdf().set(opt).from(element).save().then(() => {
                header.style.display = 'none'; // Ẩn lại tiêu đề trên web sau khi tải
            });
        }
    </script>

    <style>
        .print-header { display: none; }
        @media print {
            .no-print { display: none !important; }
            .print-header { display: block !important; }
            .page-break { page-break-after: always; border: none !important; box-shadow: none !important; }
        }
    </style>
</x-filament-panels::page>