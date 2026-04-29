<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use Illuminate\Http\Request;

class SecurityLogController extends Controller
{
    public function index()
    {
        // جلب السجلات مرتبة من الأحدث للأقدم مع بيانات الموظف
        $logs = SecurityLog::with('user')->latest()->paginate(20);
        
        return view('admin.security_logs.index', compact('logs'));
    }
}