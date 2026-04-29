<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ZktecoDevice;
use App\Models\Branch;
use Illuminate\Http\Request;

class ZktecoDeviceController extends Controller
{
    public function index()
    {
        $devices = ZktecoDevice::with('branch')->paginate(10);
        return view('admin.zkteco_device.index', compact('devices'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', 1)->get();
        return view('admin.zkteco_device.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'ip_address' => 'required|ip',
            'port' => 'required|integer',
            'branch_id' => 'nullable|exists:branches,id',
            'is_active' => 'required|boolean',
        ]);

        ZktecoDevice::create($request->except(['_token', '_method']));
        return redirect()->route('admin.zkteco-devices.index')->with('success', 'تم إضافة جهاز البصمة بنجاح');
    }

    public function edit($id)
    {
        $device = ZktecoDevice::findOrFail($id);
        $branches = Branch::where('is_active', 1)->get();
        return view('admin.zkteco_device.edit', compact('device', 'branches'));
    }

    public function update(Request $request, $id)
    {
        $device = ZktecoDevice::findOrFail($id);
        $device->update($request->except(['_token', '_method']));
        return redirect()->route('admin.zkteco-devices.index')->with('success', 'تم تحديث بيانات الجهاز بنجاح');
    }

    public function delete($id)
    {
        ZktecoDevice::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'تم حذف الجهاز بنجاح');
    }

    public function toggleStatus($id)
    {
        $device = ZktecoDevice::findOrFail($id);
        $device->update(['is_active' => !$device->is_active]);
        return redirect()->back()->with('success', 'تم تغيير حالة الجهاز بنجاح');
    }
}