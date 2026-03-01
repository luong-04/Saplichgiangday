<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra cứu Thời khóa biểu - THPT Nguyễn Bỉnh Khiêm</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Be Vietnam Pro', sans-serif; }
        
        body {
            background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 25%, #ecfdf5 50%, #f0fdf4 75%, #eff6ff 100%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15), 0 4px 20px rgba(59, 130, 246, 0.1);
        }

        .btn-search {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }
        .btn-search:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.35);
        }

        .btn-excel {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            transition: all 0.3s ease;
        }
        .btn-excel:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(5, 150, 105, 0.35);
        }

        .btn-print {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }
        .btn-print:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.35);
        }

        .btn-admin {
            background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
            transition: all 0.3s ease;
        }
        .btn-admin:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(124, 58, 237, 0.35);
        }

        .grid-table {
            border-collapse: separate;
            border-spacing: 0;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        }
        .grid-table th {
            background: linear-gradient(135deg, #eff6ff, #e0f2fe);
            padding: 14px 10px;
            font-size: 0.75rem;
            font-weight: 700;
            color: #1e40af;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #bfdbfe;
        }
        .grid-table td {
            padding: 8px;
            border-bottom: 1px solid #f1f5f9;
            border-right: 1px solid #f1f5f9;
            vertical-align: middle;
            background: white;
            transition: background 0.2s ease;
        }
        .grid-table tr:hover td {
            background: #f8fafc;
        }

        .cell-filled {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border-radius: 0.75rem;
            padding: 10px 8px;
            border-left: 3px solid #3b82f6;
            min-height: 60px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .hero-decoration {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.3;
            pointer-events: none;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .glass-card { background: white !important; backdrop-filter: none !important; border: none !important; box-shadow: none !important; }
        }
    </style>
</head>
<body class="antialiased">

    <!-- Decorative blobs -->
    <div class="hero-decoration" style="width: 400px; height: 400px; background: #93c5fd; top: -100px; left: -100px;"></div>
    <div class="hero-decoration" style="width: 300px; height: 300px; background: #6ee7b7; top: 100px; right: -50px;"></div>
    <div class="hero-decoration" style="width: 250px; height: 250px; background: #c4b5fd; bottom: 100px; left: 20%;"></div>

    <div class="relative min-h-screen flex flex-col">
        <!-- Top Bar -->
        <nav class="no-print w-full px-6 py-4">
            <div class="max-w-6xl mx-auto flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-700 flex items-center justify-center shadow-lg shadow-blue-200">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 leading-none">THPT Nguyễn Bỉnh Khiêm</h2>
                        <p class="text-[10px] text-blue-600 font-semibold uppercase tracking-widest mt-0.5">Hệ thống Thời Khóa Biểu</p>
                    </div>
                </div>
                <a href="/admin" class="btn-admin text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider shadow-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Quản lý
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col items-center px-4 pb-12">
            <!-- Hero Section -->
            <div class="text-center mt-6 mb-8 max-w-2xl">
                <div class="float-animation inline-flex items-center gap-2 bg-blue-50 border border-blue-100 rounded-full px-4 py-1.5 mb-5">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    <span class="text-xs font-semibold text-blue-700">Hệ thống đang hoạt động</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800 leading-tight mb-3">
                    Tra cứu <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">Thời Khóa Biểu</span>
                </h1>
                <p class="text-slate-500 text-sm leading-relaxed max-w-md mx-auto">
                    Nhập mã định danh giáo viên hoặc lớp học để xem lịch dạy/học chi tiết
                </p>
            </div>

            <!-- Search Card -->
            <div class="w-full max-w-2xl glass-card rounded-2xl shadow-xl shadow-blue-100/50 p-6 sm:p-8 mb-8">
                <form action="{{ route('search') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    <div class="relative flex-1">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text" name="lookup_code" value="{{ $code ?? '' }}" 
                            placeholder="Nhập mã định danh (VD: GV_A, K10_A1)..." 
                            class="search-input w-full pl-12 pr-4 py-4 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-400 bg-white/80 text-slate-800 font-medium placeholder:text-slate-400 transition-all duration-300" 
                            required>
                    </div>
                    <button type="submit" class="btn-search text-white font-bold py-4 px-8 rounded-xl shadow-lg flex items-center justify-center gap-2 whitespace-nowrap">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Tìm kiếm
                    </button>
                </form>

                <!-- Gợi ý -->
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="text-xs text-slate-400 font-medium py-1">Gợi ý:</span>
                    <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-medium">GV_A → Giáo viên</span>
                    <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-medium">K10_A1 → Lớp 10A1</span>
                </div>

                @if(session('error'))
                    <div class="mt-4 p-4 bg-red-50 border border-red-100 text-red-600 rounded-xl text-center font-semibold text-sm flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif
            </div>

            <!-- Kết quả TKB dạng Grid -->
            @if(isset($schedules))
                <div id="printArea" class="w-full max-w-6xl glass-card rounded-2xl shadow-xl shadow-blue-100/50 overflow-hidden">
                    <!-- Header kết quả -->
                    <div class="p-6 sm:p-8 border-b border-slate-100">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-md">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-extrabold text-slate-800">
                                            Lịch {{ $type === 'Giáo viên' ? 'dạy' : 'học' }} — <span class="text-blue-600">{{ $targetName }}</span>
                                        </h2>
                                        @if($type === 'Lớp' && !empty($gvcn))
                                            <p class="text-xs text-slate-500 font-medium mt-0.5">GVCN: <span class="text-blue-600 font-bold">{{ $gvcn }}</span></p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="no-print flex items-center gap-2">
                                @if($type === 'Lớp')
                                    <a href="{{ route('export.excel', $code) }}" class="btn-excel text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider shadow-lg flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Tải Excel
                                    </a>
                                @endif
                                <button onclick="printSchedule()" class="btn-print text-white px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider shadow-lg flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    In nhanh
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Grid Table -->
                    <div class="p-4 sm:p-8">
                        @if($schedules->isEmpty())
                            <div class="text-center py-16">
                                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-slate-400 font-medium">Chưa có dữ liệu thời khóa biểu.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto rounded-xl border border-slate-200">
                                <table class="grid-table w-full text-center table-fixed" style="min-width: 850px;">
                                    <thead>
                                        <tr>
                                            <th class="w-20 border-r border-blue-100">Tiết</th>
                                            @for($d = 2; $d <= 7; $d++)
                                                <th class="border-r border-blue-100">Thứ {{ $d }}</th>
                                            @endfor
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($p = 1; $p <= 10; $p++)
                                            @if($p == 6)
                                                <tr>
                                                    <td colspan="7" class="!py-2 text-xs font-bold text-slate-400 uppercase tracking-[0.3em] !bg-gradient-to-r from-slate-50 to-slate-100 text-center border-b border-slate-200">
                                                        ☀ Nghỉ trưa
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td class="border-r border-slate-100 !bg-slate-50/50">
                                                    <div class="font-extrabold text-lg text-slate-300 italic leading-none">{{ $p }}</div>
                                                    <div class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-0.5">{{ $p <= 5 ? 'Sáng' : 'Chiều' }}</div>
                                                </td>
                                                @for($d = 2; $d <= 7; $d++)
                                                    <td class="h-24">
                                                        @if(isset($grid[$d][$p]))
                                                            <div class="cell-filled text-left">
                                                                <div class="font-bold text-sm text-slate-800 leading-tight uppercase tracking-tight">{{ $grid[$d][$p]['subject'] }}</div>
                                                                <div class="text-[11px] text-blue-600 font-semibold mt-1 italic">{{ $grid[$d][$p]['extra'] }}</div>
                                                            </div>
                                                        @else
                                                            <div class="w-full h-full bg-slate-50/30 rounded-lg"></div>
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
            @endif
        </main>

        <!-- Footer -->
        <footer class="no-print w-full py-6 border-t border-slate-200/50">
            <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-xs text-slate-400 font-medium">© {{ date('Y') }} Trường THPT Nguyễn Bỉnh Khiêm — Hệ thống quản lý Thời khóa biểu</p>
                <div class="flex items-center gap-1 text-xs text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    <span class="font-medium">Powered by Laravel</span>
                </div>
            </div>
        </footer>
    </div>

    <script>
        function printSchedule() {
            const content = document.getElementById('printArea').innerHTML;
            const win = window.open('', '_blank');
            win.document.write('<html><head><title>In Thời Khóa Biểu - {{ $targetName ?? "" }}</title>');
            win.document.write('<style>');
            win.document.write(`
                * { font-family: 'Be Vietnam Pro', Arial, sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
                body { padding: 30px; }
                .no-print { display: none !important; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
                td, th { border: 1px solid #ccc; padding: 10px; text-align: center; font-size: 11px; word-wrap: break-word; }
                th { background: #f0f9ff; font-weight: 700; text-transform: uppercase; color: #1e40af; }
                .cell-filled { padding: 6px; text-align: left; }
                .cell-filled div:first-child { font-weight: 700; font-size: 12px; }
                .cell-filled div:last-child { font-size: 10px; color: #2563eb; margin-top: 2px; }
                h1 { font-size: 22px; text-align: center; text-transform: uppercase; margin: 0; }
                h2 { font-size: 16px; text-align: center; margin-top: 10px; font-weight: 600; }
                .school-name { text-align: center; color: #666; font-size: 11px; text-transform: uppercase; letter-spacing: 3px; margin-top: 6px; }
                hr { margin: 15px 0; border-color: #e2e8f0; }
            `);
            win.document.write('</style></head><body>');
            win.document.write('<h1>Thời Khóa Biểu {{ $type ?? "" }}: {{ $targetName ?? "" }}</h1>');
            @if(isset($type) && $type === 'Lớp' && !empty($gvcn))
                win.document.write('<h2>GVCN: {{ $gvcn }}</h2>');
            @endif
            win.document.write('<p class="school-name">Trường THPT Nguyễn Bỉnh Khiêm</p>');
            win.document.write('<hr>');
            win.document.write(content);
            win.document.write('</body></html>');
            win.document.close();
            win.focus();
            setTimeout(function() { win.print(); win.close(); }, 600);
        }
    </script>
</body>
</html>