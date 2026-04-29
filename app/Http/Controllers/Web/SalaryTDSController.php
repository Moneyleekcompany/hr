<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Helpers\PayrollHelper;
use App\Http\Controllers\Controller;
use App\Requests\Payroll\SalaryTDS\SalaryTDSUpdateRequest;
use App\Requests\Payroll\SalaryTDS\SalaryTDSStoreRequest;
use App\Services\Payroll\SalaryTDSService;
use Exception;

class SalaryTDSController extends Controller
{
    private $view = 'admin.payrollSetting.salaryTDS.';

    public function __construct(public SalaryTDSService $salaryTDSService)
    {
    }

    public function index()
    {
        try {
            $select = ['*'];
            $salaryTDSList = $this->salaryTDSService->getAllSalaryTDSListGroupByMaritalStatus($select);
            $singleSalaryTDS = $salaryTDSList->get('single', collect());
            $marriedSalaryTDS = $salaryTDSList->get('married', collect());

            return view($this->view . 'index', compact('singleSalaryTDS','marriedSalaryTDS'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }


    public function create()
    {
        try {
            $this->authorize('add_tds');
            return view($this->view . 'create');
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }


    public function store(SalaryTDSStoreRequest $request)
    {
        try {
            $this->authorize('add_tds');
            $validatedData = $request->validated();
            $this->salaryTDSService->store($validatedData);
            return AppHelper::sendSuccessResponse(__('message.salary_tds_add'));
        } catch (Exception $e) {
           return AppHelper::sendErrorResponse($e->getMessage(),$e->getCode());
        }
    }


    public function edit($id)
    {
        try {
            $this->authorize('edit_tds');
            $salaryTDSDetail = $this->salaryTDSService->findSalaryTDSById($id);
            return view($this->view . 'edit',compact('salaryTDSDetail'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }


    public function update(SalaryTDSUpdateRequest $request, $id)
    {
        try{
            $this->authorize('edit_tds');
            $validatedData = $request->validated();
            $salaryTDSDetail = $this->salaryTDSService->findSalaryTDSById($id);
            $this->salaryTDSService->updateDetail($salaryTDSDetail,$validatedData);
            return redirect()
                ->route('admin.salary-tds.index')
                ->with('success', __('message.salary_tds_update'));
        }catch(Exception $exception){
            return redirect()
                ->back()
                ->with('danger', $exception->getMessage());
        }
    }


    public function deleteSalaryTDS($id)
    {
        try {
            $this->authorize('delete_tds');
            $select = ['*'];
            $salaryTDSDetail = $this->salaryTDSService->findSalaryTDSById($id, $select);
            $this->salaryTDSService->deleteSalaryTDSDetail($salaryTDSDetail);
            return redirect()
                ->back()
                ->with('success', __('message.salary_tds_delete'));
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->with('danger', $exception->getMessage());
        }
    }

    public function toggleSalaryTDSStatus($id)
    {
        try {
            $this->authorize('edit_tds');
            $select = ['*'];
            $salaryTDSDetail = $this->salaryTDSService->findSalaryTDSById($id, $select);
            $this->salaryTDSService->changeSalaryTDSStatus($salaryTDSDetail);
            return redirect()
                ->back()
                ->with('success', __('message.status_changed'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }
}
