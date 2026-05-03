<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\KpiEvaluation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class KpiController extends Controller
{
    private $view = 'admin.kpi.';

    public function index()
    {
        try {
            $evaluations = KpiEvaluation::with(['user', 'evaluator'])->orderBy('year', 'desc')->orderBy('month', 'desc')->get();
            return view($this->view . 'index', compact('evaluations'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function create()
    {
        try {
            // جلب الموظفين النشطين فقط
            $employees = User::where('is_active', 1)->select('id', 'name')->get();
            return view($this->view . 'create', compact('employees'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'month' => 'required|numeric|min:1|max:12',
                'year' => 'required|numeric|min:2000|max:2099',
                'attendance_score' => 'required|numeric|min:0|max:100',
                'task_score' => 'required|numeric|min:0|max:100',
                'direct_manager_score' => 'required|numeric|min:0|max:100',
                'feedback' => 'nullable|string'
            ]);

            // حساب المتوسط المرجح أو البسيط (هنا اخترنا المتوسط البسيط من 100)
            $totalScore = ($validated['attendance_score'] + $validated['task_score'] + $validated['direct_manager_score']) / 3;
            
            $validated['total_score'] = round($totalScore, 2);
            $validated['evaluator_id'] = auth()->id();
            
            // التأكد من إضافة الصفر للأشهر من 1 إلى 9 (مثل 01، 02)
            $validated['month'] = str_pad($validated['month'], 2, '0', STR_PAD_LEFT);

            // استخدام updateOrCreate لضمان عدم وجود تقييمين لنفس الموظف في نفس الشهر
            KpiEvaluation::updateOrCreate(
                ['user_id' => $validated['user_id'], 'month' => $validated['month'], 'year' => $validated['year']],
                $validated
            );

            return redirect()->route('admin.kpi.index')->with('success', __('index.kpi_added'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        try {
            $kpi = KpiEvaluation::findOrFail($id);
            $employees = User::where('is_active', 1)->select('id', 'name')->get();
            return view($this->view . 'create', compact('kpi', 'employees'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $kpi = KpiEvaluation::findOrFail($id);
            $kpi->delete();
            return redirect()->back()->with('success', __('index.kpi_deleted'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }
}