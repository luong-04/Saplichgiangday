<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tra cứu Thời khóa biểu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased min-h-screen flex flex-col items-center pt-10 px-4">

    <div class="w-full max-w-2xl bg-white rounded-xl shadow-md overflow-hidden p-6 mb-8">
        <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">TRA CỨU THỜI KHÓA BIỂU</h1>
        
        <form action="{{ route('search') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
            @csrf
            <input type="text" name="lookup_code" value="{{ $code ?? '' }}" placeholder="Nhập mã định danh (VD: GV_A, K10_A1)..." 
                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                Tìm kiếm
            </button>
        </form>

        @if(session('error'))
            <div class="mt-4 p-4 bg-red-100 text-red-700 rounded-lg text-center font-medium">
                {{ session('error') }}
            </div>
        @endif
    </div>

    @if(isset($schedules))
        <div class="w-full max-w-4xl bg-white rounded-xl shadow-md overflow-hidden p-6">
            <div class="flex justify-between items-center mb-6 border-b pb-4">
                <h2 class="text-xl font-bold text-gray-800">
                    Lịch dạy/học của {{ $type }}: <span class="text-blue-600">{{ $targetName }}</span>
                </h2>
                <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded shadow text-sm font-semibold">
                    Tải PDF
                </button>
            </div>

            @if($schedules->isEmpty())
                <p class="text-center text-gray-500 italic py-4">Chưa có dữ liệu thời khóa biểu.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-200 text-gray-700 uppercase text-sm">
                                <th class="py-3 px-4 border-b">Thứ</th>
                                <th class="py-3 px-4 border-b">Tiết</th>
                                <th class="py-3 px-4 border-b">Môn học</th>
                                @if($type === 'Giáo viên')
                                    <th class="py-3 px-4 border-b">Lớp</th>
                                @else
                                    <th class="py-3 px-4 border-b">Giáo viên</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                <tr class="hover:bg-gray-50 transition border-b">
                                    <td class="py-3 px-4 font-semibold text-gray-800">Thứ {{ $schedule->day }}</td>
                                    <td class="py-3 px-4 text-gray-600">Tiết {{ $schedule->period }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold">
                                            {{ $schedule->subject->name }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-700">
                                        {{ $type === 'Giáo viên' ? $schedule->classRoom->name : $schedule->teacher->name }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

</body>
</html>