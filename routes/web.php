<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

// Trang chủ hiện ô nhập mã tra cứu
Route::get('/', [SearchController::class , 'index'])->name('home');

// Xử lý khi người dùng bấm nút "Tìm kiếm"
Route::post('/search', [SearchController::class , 'search'])->name('search');


// Xuất Excel từ trang tra cứu
Route::get('/export-excel/{code}', [SearchController::class , 'exportExcel'])->name('export.excel');

// ==========================================
// ADMIN ROUTES (Lại viết từ đầu sau khi bỏ Filament)
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    // Auth Routes
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class , 'showLoginForm'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class , 'login'])->name('login.submit');
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class , 'logout'])->name('logout');

    // Protected Routes
    Route::middleware('auth')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class , 'index'])->name('dashboard');

            // TODO: Thêm các Resource / Custom Router cho Giáo viên, Lớp học, Môn, Matrix ở đây
            // Các route rỗng tạm thời để tránh lỗi view gọi route() name
            Route::resource('teachers', \App\Http\Controllers\Admin\TeacherController::class)->except(['show']);
            Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class)->except(['show']);
            Route::resource('class-rooms', \App\Http\Controllers\Admin\ClassRoomController::class)->except(['show']);
            Route::resource('curricula', \App\Http\Controllers\Admin\CurriculumController::class)->except(['show']);
            Route::resource('room-categories', \App\Http\Controllers\Admin\RoomCategoryController::class)->except(['show']);
            Route::resource('rooms', \App\Http\Controllers\Admin\RoomController::class)->except(['show']);
            Route::resource('fixed-periods', \App\Http\Controllers\Admin\FixedPeriodController::class)->except(['show']);
            Route::resource('settings', \App\Http\Controllers\Admin\SettingController::class)->except(['show']);

            Route::get('/timetable/matrix', [\App\Http\Controllers\Admin\TimetableController::class , 'matrix'])->name('timetable.matrix');
            Route::get('/timetable/export', [\App\Http\Controllers\Admin\TimetableController::class , 'matrixExport'])->name('timetable.export');
            Route::post('/timetable/auto-schedule', [\App\Http\Controllers\Admin\TimetableController::class , 'autoSchedule'])->name('timetable.auto');
            Route::get('/timetable/view', [\App\Http\Controllers\Admin\TimetableController::class , 'viewTimetables'])->name('timetable.view');

            // Các route AJAX cho ma trận TKB
            Route::patch('/timetable/update/{id}', [\App\Http\Controllers\Admin\TimetableController::class , 'updateSchedule'])->name('timetable.update');
            Route::post('/timetable/swap', [\App\Http\Controllers\Admin\TimetableController::class , 'swapSchedule'])->name('timetable.swap');
            Route::post('/timetable/assign', [\App\Http\Controllers\Admin\TimetableController::class , 'assignSchedule'])->name('timetable.assign');
            Route::delete('/timetable/delete/{id}', [\App\Http\Controllers\Admin\TimetableController::class , 'deleteSchedule'])->name('timetable.delete');

            // API Routes for Timetable Matrix
            Route::get('/api/rooms/available', [\App\Http\Controllers\Admin\RoomController::class , 'availableRooms'])->name('api.rooms.available');
            Route::get('/api/teachers/{teacher}/busy-slots', [\App\Http\Controllers\Admin\TeacherController::class , 'busySlots'])->name('api.teachers.busy-slots');
        }
        );
    });