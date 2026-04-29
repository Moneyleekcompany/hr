<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Rats\Zkteco\Lib\ZKTeco;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Branch;
use Exception;

class ZKTecoController extends Controller
{
    public function syncAttendance()
    {
        try {
            \App\Jobs\SyncZktecoAttendanceJob::dispatch();
            return redirect()->back()->with('success', 'تم بدء سحب البصمات في الخلفية بنجاح! سيتم تحديث السجلات تلقائياً.');
        } catch (Exception $e) {
            return redirect()->back()->with('danger', 'فشل في بدء عملية السحب: ' . $e->getMessage());
        }
    }
}