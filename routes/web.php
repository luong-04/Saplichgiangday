<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;

// Trang chủ hiện ô nhập mã tra cứu
Route::get('/', [SearchController::class , 'index'])->name('home');

// Xử lý khi người dùng bấm nút "Tìm kiếm"
Route::post('/search', [SearchController::class , 'search'])->name('search');

// Xuất Excel từ trang tra cứu
Route::get('/export-excel/{code}', [SearchController::class , 'exportExcel'])->name('export.excel');