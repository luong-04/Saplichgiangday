<x-filament-panels::page>

<style>
    .matrix-layout { display: flex; flex-direction: column; gap: 1.5rem; }
    @media (min-width: 1024px) {
        .matrix-layout { flex-direction: row; align-items: flex-start; }
        .matrix-left { width: 30%; position: sticky; top: 1.5rem; }
        .matrix-right { width: 70%; }
    }
    .matrix-card {
        background: linear-gradient(145deg, rgba(255,255,255,0.94), rgba(240,249,255,0.9));
        backdrop-filter: blur(16px);
        border: 1px solid rgba(186,230,253,0.5);
        border-radius: 1rem;
        box-shadow: 0 4px 20px rgba(14,165,233,0.06);
        padding: 1.25rem;
        transition: all 0.3s ease;
    }
    .matrix-card:hover { box-shadow: 0 10px 36px rgba(14,165,233,0.1); }
    .step-heading {
        display: flex; align-items: center; gap: 0.75rem;
        font-size: 1.1rem; font-weight: 900; color: #0c4a6e; margin-bottom: 1.25rem;
    }
    .step-number {
        width: 2rem; height: 2rem;
        background: linear-gradient(135deg, #2563eb, #0ea5e9);
        color: white; border-radius: 0.6rem;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.85rem; font-weight: 900;
        box-shadow: 0 4px 12px rgba(37,99,235,0.3);
    }
    .form-label {
        display: block; font-size: 0.85rem; font-weight: 700; color: #64748b;
        text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 0.5rem;
    }
    .form-select {
        width: 100%; border: 1px solid rgba(186,230,253,0.6); border-radius: 0.5rem;
        padding: 0.5rem 2rem 0.5rem 0.75rem; font-size: 0.85rem; font-weight: 600;
        background-color: white;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center; background-repeat: no-repeat; background-size: 1.25em 1.25em;
        -webkit-appearance: none; -moz-appearance: none; appearance: none;
        transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.04); color: #334155;
    }
    .form-select:focus { border-color: #38bdf8; box-shadow: 0 0 0 3px rgba(56,189,248,0.2); outline: none; }
    .drag-zone {
        background: linear-gradient(135deg, rgba(240,249,255,0.6), rgba(236,253,245,0.4));
        border: 2px dashed rgba(186,230,253,0.5); border-radius: 1rem;
        padding: 1rem; min-height: 160px; transition: all 0.3s ease;
    }
    .drag-zone.empty {
        display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8;
    }
    .drag-zone-icon { font-size: 2.5rem; margin-bottom: 0.75rem; opacity: 0.4; }
    .drag-zone-text { font-size: 0.9rem; font-weight: 600; text-align: center; }
    .drag-card {
        background: linear-gradient(135deg, #eff6ff, #e0f2fe);
        border: 1px solid rgba(186,230,253,0.6); border-radius: 0.75rem;
        padding: 0.75rem 1rem; margin-bottom: 0.5rem; cursor: grab;
        transition: all 0.3s ease; font-weight: 700; font-size: 0.9rem; color: #0c4a6e;
    }
    .drag-card:hover { background: linear-gradient(135deg, #dbeafe, #bae6fd); box-shadow: 0 4px 14px rgba(59,130,246,0.15); transform: translateY(-1px); }
    .drag-card:active { cursor: grabbing; transform: scale(0.97); }
    .drag-card-sub { font-size: 0.75rem; color: #2563eb; font-weight: 600; margin-top: 2px; }
    .matrix-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .matrix-table thead th {
        background: linear-gradient(135deg, #eff6ff, #e0f2fe);
        color: #1e40af; font-weight: 800; font-size: 0.85rem;
        text-transform: uppercase; letter-spacing: 0.05em;
        padding: 0.85rem 0.5rem; text-align: center;
        border-bottom: 2px solid rgba(186,230,253,0.5); position: sticky; top: 0; z-index: 10;
    }
    .matrix-table tbody td {
        padding: 0.15rem; border-bottom: 1px solid rgba(241,245,249,0.8);
        border-right: 1px solid rgba(241,245,249,0.6); height: 3.5rem;
        text-align: center; vertical-align: middle; transition: background 0.15s ease;
    }
    .cell-period-m { font-weight: 900; color: #bae6fd; font-size: 1.1rem; font-style: italic; background: rgba(248,250,252,0.5) !important; width: 2.5rem; }
    .cell-filled {
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-left: 3px solid #3b82f6; border-radius: 0.4rem;
        padding: 0.25rem 0.4rem; text-align: left;
        display: flex; flex-direction: column; justify-content: center; align-items: flex-start;
        height: 100%; position: relative;
    }
    .cell-filled.cell-fixed { background: linear-gradient(135deg, #fef3c7, #fde68a); border-left: 3px solid #f59e0b; }
    .cell-fixed-badge { position: absolute; top: 2px; left: 4px; font-size: 0.5rem; font-weight: 900; color: #92400e; text-transform: uppercase; }
    .cell-filled-sub { font-weight: 800; font-size: 0.72rem; color: #0c4a6e; text-transform: uppercase; }
    .cell-filled-tea { font-size: 0.65rem; color: #2563eb; font-weight: 700; font-style: italic; margin-top: 1px; }
    .cell-filled-room { font-size: 0.55rem; color: #059669; font-weight: 700; margin-top: 1px; }
    .cell-delete {
        position: absolute; top: 2px; right: 2px; width: 1.1rem; height: 1.1rem;
        border-radius: 0.35rem; background: rgba(239,68,68,0.1); color: #ef4444;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
        opacity: 0; transition: opacity 0.2s; font-size: 0.65rem; font-weight: 900; border: none;
    }
    .cell-filled:hover .cell-delete { opacity: 1; }
    .cell-fixed:hover .cell-delete { opacity: 0 !important; }
    .cell-delete:hover { background: #ef4444; color: white; }
    .cell-empty {
        border: 2px dashed transparent; border-radius: 0.5rem;
        height: 100%; display: flex; align-items: center; justify-content: center;
        transition: all 0.2s ease; cursor: pointer; min-height: 3rem;
    }
    .cell-empty:hover { border-color: rgba(59,130,246,0.3); background: rgba(240,249,255,0.3); }
    .cell-empty.dragover { border-color: #3b82f6; background: rgba(224,242,254,0.6); }
    .lunch-break-m { padding: 0.4rem !important; font-size: 0.7rem; font-weight: 900; color: #cbd5e1; text-transform: uppercase; letter-spacing: 0.25em; font-style: italic; background: linear-gradient(90deg, #f8fafc, #f0f9ff) !important; }
    .btn-save {
        width: 100%; padding: 0.85rem; border: none; border-radius: 0.875rem;
        font-size: 1rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
        cursor: pointer; background: linear-gradient(135deg, #2563eb, #0ea5e9);
        color: white; box-shadow: 0 6px 20px rgba(37,99,235,0.3); transition: all 0.3s ease;
        display: flex; align-items: center; justify-content: center; gap: 0.5rem;
    }
    .btn-save:hover { box-shadow: 0 10px 30px rgba(37,99,235,0.4); transform: translateY(-2px); }
    .section-divider { height: 1px; background: linear-gradient(90deg, transparent, rgba(186,230,253,0.5), transparent); margin: 1.5rem 0; }
    .double-badge { display: inline-block; font-size: 0.55rem; background: #dbeafe; color: #1d4ed8; padding: 0.1rem 0.3rem; border-radius: 0.25rem; font-weight: 800; margin-left: 0.25rem; }
    .room-badge { display: inline-block; font-size: 0.55rem; background: #d1fae5; color: #065f46; padding: 0.1rem 0.3rem; border-radius: 0.25rem; font-weight: 800; margin-left: 0.25rem; }
</style>

<div class="matrix-layout">
    <div class="matrix-left">
        <div class="matrix-card">
            <div class="step-heading"><span class="step-number">1</span> Chọn Lớp Xếp Lịch</div>
            <div style="margin-bottom:1rem;">
                <label class="form-label">Khối</label>
                <select wire:model.live="selectedGrade" class="form-select">
                    <option value="">── Chọn Khối ──</option>
                    @foreach($grades as $grade)
                        <option value="{{ $grade }}">Khối {{ $grade }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom:1.5rem;">
                <label class="form-label">Lớp học</label>
                <select wire:model.live="selectedClass" class="form-select">
                    <option value="">── Chọn Lớp ──</option>
                    @foreach($classes as $cl)
                        <option value="{{ $cl->id }}">{{ $cl->name }}
                            @if($cl->shift === 'morning') (Sáng)
                            @elseif($cl->shift === 'afternoon') (Chiều)
                            @else
                                (Cả ngày)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="section-divider"></div>

            <div class="step-heading"><span class="step-number">2</span> Tạo Thẻ Môn Học</div>
            <div style="margin-bottom:1rem;">
                <label class="form-label">Môn học</label>
                <select wire:model.live="dragSubjectId" class="form-select">
                    <option value="">── Chọn Môn ──</option>
                    @foreach($subjects as $sub)
                        @php $s = is_array($sub) ? $sub : $sub->toArray(); @endphp
                        <option value="{{ $s['id'] }}">
                            {{ $s['name'] }}
                            @if($s['is_double_period'] ?? false) [Đôi] @endif
                            @if(($s['type'] ?? '0') == '2') [TH] @endif
                            ({{ $s['lessons_per_week'] ?? '?' }}t/tuần)
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom:1rem;">
                <label class="form-label">Giáo viên</label>
                <select wire:model.live="dragTeacherId" class="form-select">
                    <option value="">── Chọn GV ──</option>
                    @foreach($filteredTeachers as $t)
                        @php $tv = is_array($t) ? $t : $t->toArray(); @endphp
                        <option value="{{ $tv['id'] }}">{{ $tv['name'] }} {{ ($tv['short_code'] ?? '') ? '('.$tv['short_code'].')' : '' }} - Còn {{ $tv['remaining_quota'] ?? 0 }}t</option>
                    @endforeach
                </select>
            </div>

            {{-- Phòng chức năng (chỉ hiện khi cần) --}}
            @if($requiresRoom)
            <div style="margin-bottom:1rem;">
                <label class="form-label">🏢 Phòng chức năng</label>
                <select wire:model.live="dragRoomId" class="form-select" style="border-color: #059669;">
                    <option value="">── Chọn Phòng ──</option>
                    @foreach($filteredRooms as $r)
                        @php $rv = is_array($r) ? $r : $r->toArray(); @endphp
                        <option value="{{ $rv['id'] }}">{{ $rv['name'] }} (sức chứa: {{ $rv['capacity'] ?? 1 }})</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Khu vực thẻ kéo --}}
            <div class="drag-zone {{ ($dragTeacherId && $dragSubjectId) ? '' : 'empty' }}">
                @if($dragTeacherId && $dragSubjectId)
                    @php
                        $subObj = collect($subjects)->firstWhere('id', $dragSubjectId);
                        $subName = is_array($subObj) ? ($subObj['name'] ?? '') : ($subObj->name ?? '');
                        $isDouble = is_array($subObj) ? ($subObj['is_double_period'] ?? false) : ($subObj->is_double_period ?? false);
                        $needsRoom = is_array($subObj) ? (($subObj['type'] ?? '0') == '2') : ($subObj && $subObj->type == '2');
                        $teaObj = collect($filteredTeachers)->firstWhere('id', $dragTeacherId);
                        $teaName = is_array($teaObj) ? (($teaObj['short_code'] ?? '') ?: ($teaObj['name'] ?? '')) : ($teaObj ? ($teaObj->short_code ?: $teaObj->name) : '');
                        $remaining = is_array($teaObj) ? ($teaObj['remaining_quota'] ?? 0) : ($teaObj->remaining_quota ?? 0);
                        $roomObj = collect($filteredRooms)->firstWhere('id', $dragRoomId);
                        $roomName = is_array($roomObj) ? ($roomObj['name'] ?? '') : ($roomObj->name ?? '');
                    @endphp
                    <div class="drag-card" id="drag-card" draggable="true"
                         ondragstart="event.dataTransfer.setData('text/plain', '{{ $dragTeacherId }}_{{ $dragSubjectId }}')">
                        <div>📚 {{ $subName }}
                            @if($isDouble)<span class="double-badge">TIẾT ĐÔI</span>@endif
                            @if($needsRoom && $roomObj)<span class="room-badge">🏢 {{ $roomName }}</span>@endif
                        </div>
                        <div class="drag-card-sub" style="display:flex; justify-content:space-between;">
                            <span>👨‍🏫 {{ $teaName }}</span>
                            <span style="font-size:0.7rem; color:#3b82f6; background:#eff6ff; padding:0.1rem 0.4rem; border-radius:0.3rem;">Còn {{ $remaining }}</span>
                        </div>
                    </div>
                    <p style="font-size:0.75rem; color:#94a3b8; text-align:center; margin-top:0.75rem; font-weight:600;">
                        Kéo thẻ này vào ô trống trong bảng bên phải
                    </p>
                @else
                    <div class="drag-zone-icon">↕️</div>
                    <div class="drag-zone-text">Chọn Giáo viên & Môn học<br>để tạo thẻ xếp lịch</div>
                @endif
            </div>

            <div class="section-divider"></div>
            <button wire:click="saveTimetable" class="btn-save">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Lưu Thời Khóa Biểu
            </button>
        </div>
    </div>

    {{-- CỘT PHẢI: BẢNG MA TRẬN --}}
    <div class="matrix-right">
        <div class="matrix-card" style="padding: 1.25rem;">
            @if(!$selectedClass)
                <div style="text-align:center; padding:4rem 2rem; color:#94a3b8;">
                    <div style="font-size:3.5rem; margin-bottom:1rem; opacity:0.3;">📅</div>
                    <div style="font-size:1.2rem; font-weight:900; color:#64748b;">Bảng thời khóa biểu đang trống</div>
                    <div style="font-size:0.9rem; font-weight:600;">Chọn <strong style="color:#6366f1;">Khối</strong> và <strong style="color:#6366f1;">Lớp</strong> để bắt đầu.</div>
                </div>
            @else
                <div style="overflow-x:auto; border-radius:0.75rem; border:1px solid rgba(199,210,254,0.4);">
                    <table class="matrix-table">
                        <thead>
                            <tr>
                                <th style="width:2.5rem;">Tiết</th>
                                @for($d=$daysStart; $d<=$daysEnd; $d++) <th>Thứ {{ $d }}</th> @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for($p=1; $p<=$periodsPerDay; $p++)
                                @if($p == $lunchAfterPeriod + 1)
                                    <tr><td colspan="{{ $daysEnd - $daysStart + 2 }}" class="lunch-break-m">── Nghỉ trưa ──</td></tr>
                                @endif
                                <tr>
                                    <td class="cell-period-m">{{ $p }}</td>
                                    @for($d=$daysStart; $d<=$daysEnd; $d++)
                                        <td>
                                            @if(isset($matrix[$d][$p]))
                                                @php $cell = $matrix[$d][$p]; @endphp
                                                <div class="cell-filled {{ ($cell['is_fixed'] ?? false) ? 'cell-fixed' : '' }}">
                                                    @if($cell['is_fixed'] ?? false)
                                                        <span class="cell-fixed-badge">🔒 Cố định</span>
                                                    @endif
                                                    <div class="cell-filled-sub">{{ $cell['subject'] }}</div>
                                                    <div class="cell-filled-tea">{{ $cell['teacher'] }}</div>
                                                    @if($cell['room'] ?? null)
                                                        <div class="cell-filled-room">🏢 {{ $cell['room'] }}</div>
                                                    @endif
                                                    @if(!($cell['is_fixed'] ?? false))
                                                        <button class="cell-delete" wire:click="deleteSchedule({{ $cell['id'] }})" title="Xóa">✕</button>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="cell-empty"
                                                     ondragover="event.preventDefault(); this.classList.add('dragover');"
                                                     ondragleave="this.classList.remove('dragover');"
                                                     ondrop="this.classList.remove('dragover'); var data = event.dataTransfer.getData('text/plain').split('_'); @this.call('assignSchedule', {{ $d }}, {{ $p }}, data[0], data[1]);">
                                                </div>
                                            @endif
                                        </td>
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

</x-filament-panels::page>