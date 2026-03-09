<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\ClassRoom;
use App\Models\Subject;
use App\Models\Room;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'teachers' => Teacher::count(),
            'classes' => ClassRoom::count(),
            'subjects' => Subject::count(),
            'rooms' => Room::count(),
        ];

        return view('admin.dashboard.index', compact('stats'));
    }
}
