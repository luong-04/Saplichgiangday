<table>
    <thead>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 16px; font-weight: bold;">THỜI KHÓA BIỂU LỚP {{ $tkb['name'] }}</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-style: italic;">Giáo viên chủ nhiệm: {{ $tkb['gvcn'] }}</th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center;">Trường THPT Nguyễn Bỉnh Khiêm</th>
        </tr>
        <tr>
            <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Tiết</th>
            @for($d=2; $d<=7; $d++)
                <th style="font-weight: bold; text-align: center; border: 1px solid #000000;">Thứ {{ $d }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @for($p=1; $p<=10; $p++)
            @if($p==6)
                <tr>
                    <td colspan="7" style="text-align: center; font-weight: bold; background-color: #f3f4f6; border: 1px solid #000000;">NGHỈ TRƯA</td>
                </tr>
            @endif
            <tr>
                <td style="text-align: center; font-weight: bold; border: 1px solid #000000;">{{ $p }}</td>
                @for($d=2; $d<=7; $d++)
                    <td style="text-align: center; border: 1px solid #000000;">
                        @if(isset($tkb['data'][$d][$p]))
                            {{ $tkb['data'][$d][$p]['sub'] }} - {{ $tkb['data'][$d][$p]['tea'] }}
                        @endif
                    </td>
                @endfor
            </tr>
        @endfor
    </tbody>
</table>