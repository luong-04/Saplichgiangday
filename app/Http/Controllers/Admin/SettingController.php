<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        // Xác thực cơ bản một số trường quan trọng
        $request->validate([
            'school_name' => 'required|string|max:255',
            'periods_per_day' => 'required|integer|min:1|max:20',
            'days_start' => 'required|integer|between:2,7',
            'days_end' => 'required|integer|between:2,7',
            'lunch_after_period' => 'required|integer|min:1',
            'max_consecutive_periods' => 'required|integer|min:1',
            'max_gap_periods' => 'required|integer|min:0',
            'enforce_room_assignment' => 'nullable|in:0,1',
        ]);

        // Đảm bảo checkbox boolean lưu về 0 nếu không được check
        if (!isset($data['enforce_room_assignment'])) {
            $data['enforce_room_assignment'] = 0;
        }

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        // Xóa cache hệ thống để đảm bảo nhận setting mới
        Artisan::call('cache:clear');

        return redirect()->route('admin.settings.index')->with('success', 'Cập nhật cấu hình hệ thống thành công.');
    }
}
