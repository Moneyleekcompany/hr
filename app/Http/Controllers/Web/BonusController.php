<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Requests\Payroll\Bonus\BonusRequest;
use App\Services\Payroll\BonusService;
use Exception;

class BonusController extends Controller
{
    private $view = 'admin.payrollSetting.bonus.';

    public function __construct(public BonusService $bonusService)
    {
    }

    public function index()
    {
        try {
            $select = ['*'];
            $bonusList = $this->bonusService->getAllBonusList();
            return view($this->view . 'index', compact('bonusList'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function create()
    {
        try {
//            $this->authorize('add_salary_component');
            $months = AppHelper::getMonthsList();
            return view($this->view . 'create', compact('months'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function store(BonusRequest $request)
    {
        try {
//            $this->authorize('add_salary_component');
            $validatedData = $request->validated();
            $this->bonusService->store($validatedData);
            return redirect()
                ->route('admin.bonus.index')
                ->with('success', __('message.add_bonus'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('danger', $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        try {
//            $this->authorize('edit_salary_component');
//            $select = ['*'];
            $months = AppHelper::getMonthsList();

            $bonusDetail = $this->bonusService->findBonusById($id);
            return view($this->view . 'edit', compact('bonusDetail','months'));
        } catch (Exception $exception) {
            return redirect()
                ->back()
                ->with('danger', $exception->getMessage());
        }
    }

    public function update(BonusRequest $request, $id)
    {
        try {
//            $this->authorize('edit_salary_component');
            $validatedData = $request->validated();
            $bonusDetail = $this->bonusService->findBonusById($id);
            $this->bonusService->updateDetail($bonusDetail, $validatedData);
            return redirect()
                ->route('admin.bonus.index')
                ->with('success', __('message.update_bonus'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('danger', $e->getMessage())
                ->withInput();
        }
    }

    public function delete($id)
    {
        try {
//            $this->authorize('delete_salary_component');
            $bonusDetail = $this->bonusService->findBonusById($id);
            $this->bonusService->deleteBonusDetail($bonusDetail);
            return redirect()
                ->back()
                ->with('success', __('message.delete_bonus'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function toggleBonusStatus($id)
    {
        try {
//            $this->authorize('edit_salary_component');
            $bonusDetail = $this->bonusService->findBonusById($id);
            $this->bonusService->changeBonusStatus($bonusDetail);
            return redirect()
                ->back()
                ->with('success', __('message.status_changed'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }


}
