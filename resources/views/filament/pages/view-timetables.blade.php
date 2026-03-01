<x-filament-panels::page>

{{-- CSS tùy chỉnh cho trang Thời Khóa Biểu --}}
<style>
    /* Header thanh điều hướng */
    .tkb-header {
        background: linear-gradient(135deg, rgba(255,255,255,0.92), rgba(240,249,255,0.88));
        backdrop-filter: blur(20px);
        border: 1px solid rgba(186,230,253,0.5);
        border-radius: 1.25rem;
        box-shadow: 0 8px 32px rgba(14,165,233,0.06);
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
    }

    /* Tab khối lớp */
    .grade-tabs {
        display: flex;
        gap: 0.75rem;
        background: rgba(240,249,255,0.6);
        padding: 0.5rem;
        border-radius: 1rem;
        border: 1px solid rgba(186,230,253,0.3);
    }
    .grade-tab {
        padding: 0.7rem 2rem;
        border-radius: 0.75rem;
        font-size: 0.9rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        background: transparent;
        color: #64748b;
    }
    .grade-tab:hover { background: rgba(255,255,255,0.7); color: #1e40af; }
    .grade-tab.active {
        background: linear-gradient(135deg, #2563eb, #0ea5e9);
        color: white;
        box-shadow: 0 4px 14px rgba(37,99,235,0.3);
    }

    /* Card thời khóa biểu từng lớp */
    .class-card {
        background: linear-gradient(145deg, rgba(255,255,255,0.94), rgba(240,249,255,0.9));
        backdrop-filter: blur(16px);
        border: 1px solid rgba(186,230,253,0.5);
        border-radius: 1.25rem;
        box-shadow: 0 6px 24px rgba(14,165,233,0.06);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4,0,0.2,1);
        margin-bottom: 1.25rem;
    }
    .class-card:hover {
        box-shadow: 0 12px 40px rgba(14,165,233,0.1);
        transform: translateY(-2px);
    }

    /* Header của card */
    .card-header {
        padding: 1.25rem 1.75rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background 0.2s;
    }
    .card-header:hover { background: rgba(240,249,255,0.3); }

    /* Badge tên lớp */
    .class-badge {
        width: 3.75rem;
        height: 3.75rem;
        background: linear-gradient(135deg, #2563eb, #0ea5e9);
        border-radius: 1rem;
        box-shadow: 0 6px 18px rgba(37,99,235,0.3);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.4s ease;
    }
    .class-badge.expanded { transform: scale(1.08) rotate(2deg); box-shadow: 0 8px 24px rgba(37,99,235,0.4); }
    .class-badge-label { font-size: 0.55rem; font-weight: 700; opacity: 0.7; text-transform: uppercase; }
    .class-badge-name { font-size: 1.3rem; font-weight: 900; line-height: 1; }

    /* Thông tin lớp */
    .class-info-title { font-size: 1.2rem; font-weight: 900; color: #0c4a6e; text-transform: uppercase; letter-spacing: -0.02em; }
    .class-info-gvcn { font-size: 0.85rem; font-weight: 600; color: #64748b; margin-top: 0.25rem; }
    .class-info-gvcn span { color: #2563eb; font-weight: 800; }

    /* Nút hành động */
    .action-btns { display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap; }
    .btn-excel, .btn-print {
        padding: 0.6rem 1.25rem;
        border-radius: 0.75rem;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }
    .btn-excel {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 4px 14px rgba(16,185,129,0.3);
    }
    .btn-excel:hover { box-shadow: 0 6px 20px rgba(16,185,129,0.4); transform: translateY(-2px); }
    .btn-print {
        background: linear-gradient(135deg, #2563eb, #0ea5e9);
        color: white;
        box-shadow: 0 4px 14px rgba(37,99,235,0.3);
    }
    .btn-print:hover { box-shadow: 0 6px 20px rgba(37,99,235,0.4); transform: translateY(-2px); }

    /* Chevron toggle */
    .chevron-toggle { transition: transform 0.4s ease; color: #94a3b8; }
    .chevron-toggle.rotated { transform: rotate(180deg); }

    /* Bảng TKB hiện đại */
    .modern-grid { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.85rem; }
    .modern-grid thead th {
        background: linear-gradient(135deg, #eff6ff, #e0f2fe);
        color: #1e40af;
        font-weight: 800;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        padding: 0.85rem 0.5rem;
        text-align: center;
        border-bottom: 2px solid rgba(186,230,253,0.5);
    }
    .modern-grid thead th:first-child { border-radius: 0.75rem 0 0 0; }
    .modern-grid thead th:last-child { border-radius: 0 0.75rem 0 0; }
    .modern-grid tbody td {
        padding: 0.35rem;
        text-align: center;
        border-bottom: 1px solid rgba(241,245,249,0.8);
        border-right: 1px solid rgba(241,245,249,0.6);
        height: 5.5rem;
        vertical-align: middle;
    }
    .modern-grid tbody tr:last-child td:first-child { border-radius: 0 0 0 0.75rem; }
    .modern-grid tbody tr:last-child td:last-child { border-radius: 0 0 0.75rem 0; }
    .modern-grid tbody td:hover { background: rgba(240,249,255,0.3); }

    .cell-period {
        font-weight: 900; color: #bae6fd; font-size: 1.4rem; font-style: italic;
        background: rgba(248,250,252,0.4);
    }
    .cell-content {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-left: 3px solid #3b82f6;
        border-radius: 0.5rem;
        padding: 0.5rem 0.65rem;
        text-align: left;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .cell-subject { font-weight: 800; font-size: 0.82rem; color: #0c4a6e; text-transform: uppercase; letter-spacing: -0.01em; }
    .cell-teacher { font-size: 0.72rem; color: #2563eb; font-weight: 700; font-style: italic; margin-top: 2px; }

    .lunch-break {
        padding: 0.5rem !important; font-size: 0.65rem; font-weight: 900; color: #cbd5e1;
        text-transform: uppercase; letter-spacing: 0.3em; font-style: italic;
        background: linear-gradient(90deg, #f8fafc, #f0f9ff) !important;
    }

    /* Khu vực mở rộng */
    .expand-area {
        padding: 1.5rem 2rem;
        border-top: 1px solid rgba(241,245,249,0.8);
        background: white;
    }

    /* Title nhà trường */
    .school-title {
        font-size: 1.15rem; font-weight: 900; color: #0c4a6e; text-transform: uppercase; letter-spacing: -0.02em;
    }
    .school-title-gradient {
        background: linear-gradient(135deg, #2563eb, #0ea5e9);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
    }
    .school-subtitle { font-size: 0.65rem; color: #0284c7; font-weight: 700; text-transform: uppercase; letter-spacing: 0.25em; margin-top: 0.15rem; }

    @media print {
        .no-print { display: none !important; }
        .class-card { box-shadow: none !important; border: 1px solid #e2e8f0 !important; break-inside: avoid; }
        .fi-sidebar, .fi-topbar { display: none !important; }
        body { background: white !important; }
    }
</style>

{{-- ======= HEADER BAR ======= --}}
<div class="tkb-header no-print">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <span style="font-size:0.7rem; font-weight:800; color:#94a3b8; text-transform:uppercase; letter-spacing:0.15em;">Chọn khối lớp</span>
            <div class="grade-tabs mt-2">
                @foreach(['10', '11', '12'] as $grade)
                    <button wire:click="$set('selectedGrade', '{{ $grade }}')" 
                        class="grade-tab {{ $selectedGrade == $grade ? 'active' : '' }}">
                        Khối {{ $grade }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="text-right">
            <div class="school-title">
                <span class="school-title-gradient">THPT</span> Nguyễn Bỉnh Khiêm
            </div>
            <div class="school-subtitle">Hệ thống thời khóa biểu</div>
        </div>
    </div>
</div>

{{-- ======= DANH SÁCH TKB ======= --}}
<div>
    @foreach($timetables as $index => $tkb)
        <div x-data="{ expanded: false }" class="class-card">
            
            {{-- Card Header --}}
            <div class="card-header" @click="expanded = !expanded">
                <div class="flex items-center gap-5">
                    <div class="class-badge" :class="expanded ? 'expanded' : ''">
                        <span class="class-badge-label">Lớp</span>
                        <span class="class-badge-name">{{ $tkb['name'] }}</span>
                    </div>
                    <div>
                        <div class="class-info-title">TKB Lớp {{ $tkb['name'] }}</div>
                        <div class="class-info-gvcn">GVCN: <span>{{ $tkb['gvcn'] }}</span></div>
                    </div>
                </div>

                <div class="flex items-center gap-3" @click.stop>
                    <div class="action-btns">
                        <button type="button" wire:click="exportExcel({{ $tkb['id'] }})" class="btn-excel">
                            <x-heroicon-m-arrow-down-tray class="w-4 h-4"/> Tải Excel
                        </button>
                        
                        <button type="button" onclick="printTable('area-{{ $index }}', '{{ $tkb['name'] }}', '{{ $tkb['gvcn'] }}')" class="btn-print">
                            <x-heroicon-m-printer class="w-4 h-4"/> In Nhanh
                        </button>
                    </div>
                    
                    <div class="chevron-toggle ml-2" :class="expanded ? 'rotated' : ''">
                        <x-heroicon-o-chevron-down class="w-5 h-5" />
                    </div>
                </div>
            </div>

            {{-- Expanded Grid --}}
            <div x-show="expanded" x-collapse x-cloak id="area-{{ $index }}" class="expand-area">
                <div style="overflow-x:auto; border-radius:0.75rem; border:1px solid rgba(199,210,254,0.4); box-shadow: inset 0 2px 8px rgba(0,0,0,0.03);">
                    <table class="modern-grid">
                        <thead>
                            <tr>
                                <th style="width:3.5rem;">Tiết</th>
                                @for($d=2; $d<=7; $d++) <th>Thứ {{ $d }}</th> @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for($p=1; $p<=10; $p++)
                                @if($p==6) 
                                    <tr><td colspan="7" class="lunch-break">── Nghỉ trưa ──</td></tr> 
                                @endif
                                <tr>
                                    <td class="cell-period">{{ $p }}</td>
                                    @for($d=2; $d<=7; $d++)
                                        <td>
                                            @if(isset($tkb['data'][$d][$p]))
                                                <div class="cell-content">
                                                    <div class="cell-subject">{{ $tkb['data'][$d][$p]['sub'] }}</div>
                                                    <div class="cell-teacher">{{ $tkb['data'][$d][$p]['tea'] }}</div>
                                                </div>
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

{{-- JS In nhanh --}}
@push('scripts')
<script>
function printTable(areaId, className, gvcn) {
    var el = document.getElementById(areaId);
    if (!el) return;
    var w = window.open('', '_blank');
    var content = '<html><head><title>TKB Lop ' + className + '<\/title>';
    content += '<style>';
    content += '* { margin:0; padding:0; box-sizing:border-box; }';
    content += 'body { font-family:Be Vietnam Pro,sans-serif; padding:2rem; }';
    content += 'h1 { text-align:center; font-size:1.1rem; color:#1e1b4b; text-transform:uppercase; }';
    content += 'h2 { text-align:center; font-size:1.4rem; color:#4338ca; margin:0.3rem 0; font-weight:900; }';
    content += 'p { text-align:center; font-size:0.85rem; color:#6b7280; margin-bottom:1rem; }';
    content += 'table { width:100%; border-collapse:collapse; font-size:0.8rem; margin-top:1rem; }';
    content += 'th { background:#eef2ff; color:#4338ca; font-weight:800; text-transform:uppercase; font-size:0.7rem; letter-spacing:0.05em; padding:0.6rem 0.3rem; border:1px solid #c7d2fe; }';
    content += 'td { border:1px solid #e2e8f0; padding:0.4rem; text-align:center; height:3rem; vertical-align:middle; }';
    content += '<\/style><\/head><body>';
    content += '<h1>Truong THPT Nguyen Binh Khiem<\/h1>';
    content += '<h2>Thoi Khoa Bieu Lop ' + className + '<\/h2>';
    content += '<p>GVCN: ' + gvcn + '<\/p>';
    content += el.innerHTML;
    content += '<\/body><\/html>';
    w.document.write(content);
    w.document.close();
    setTimeout(function() { w.print(); w.close(); }, 500);
}
</script>
@endpush

</x-filament-panels::page>