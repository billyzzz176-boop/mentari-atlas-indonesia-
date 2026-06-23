<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index()
    {
        // Mengambil semua data log, diurutkan dari yang paling baru, batasi 50 per halaman
        $logs = ActivityLog::with('user')->orderBy('created_at', 'asc')->paginate(50);
        
        return view('activity_log.index', compact('logs'));
    }
}