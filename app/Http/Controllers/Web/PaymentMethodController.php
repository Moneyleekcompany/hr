<?php

namespace App\Http\Controllers\Web;

use App\Helpers\AppHelper;
use App\Http\Controllers\Controller;
use App\Requests\Payroll\PaymentMethod\PaymentMethodStoreRequest;
use App\Requests\Payroll\PaymentMethod\PaymentMethodUpdateRequest;
use App\Services\Payroll\PaymentMethodService;
use Exception;

class PaymentMethodController extends Controller
{

    private $view = 'admin.payrollSetting.paymentMethod.';

    public function __construct(public PaymentMethodService $paymentMethodService)
    {
    }

    public function index()
    {
        try {
            $select = ['*'];
            $paymentMethodLists = $this->paymentMethodService->getAllPaymentMethodList($select);
            return view($this->view . 'index', compact('paymentMethodLists'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function create()
    {
        try {
            $this->authorize('add_payment_method');
            return view($this->view . 'create');
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }


    public function store(PaymentMethodStoreRequest $request)
    {
        try {
            $this->authorize('add_payment_method');
            $validatedData = $request->validated();
            $this->paymentMethodService->store($validatedData);
            return redirect()
                ->route('admin.payment-methods.index')
                ->with('success', __('message.payment_method_add'));
        } catch (Exception $e) {
            return redirect()->back()
                ->with('danger', $e->getMessage())
                ->withInput();
        }
    }


    public function update(PaymentMethodUpdateRequest $request, $id)
    {
        try {
            $this->authorize('edit_payment_method');
            $validatedData = $request->validated();
            $select = ['*'];
            $paymentMethodDetail = $this->paymentMethodService->findPaymentMethodById($id, $select);
            $update = $this->paymentMethodService->updateDetail($paymentMethodDetail, $validatedData);
            return AppHelper::sendSuccessResponse(__('message.payment_method_update'),$update);
        } catch (Exception $e) {
            return AppHelper::sendErrorResponse($e->getMessage(),$e->getCode());
        }
    }

    public function deletePaymentMethod($id)
    {
        try {
            $this->authorize('delete_payment_method');
            $select = ['*'];
            $paymentMethodDetail = $this->paymentMethodService->findPaymentMethodById($id, $select);
            $this->paymentMethodService->deletePaymentMethodDetail($paymentMethodDetail);
            return redirect()
                ->back()
                ->with('success', __('message.payment_method_delete'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }

    public function togglePaymentMethodStatus($id)
    {
        try {
            $this->authorize('edit_payment_method');
            $select = ['*'];
            $paymentMethodDetail = $this->paymentMethodService->findPaymentMethodById($id,$select);
            $this->paymentMethodService->changePaymentMethodStatus($paymentMethodDetail);
            return redirect()
                ->back()
                ->with('success', __('message.status_changed'));
        } catch (Exception $exception) {
            return redirect()->back()->with('danger', $exception->getMessage());
        }
    }
}
