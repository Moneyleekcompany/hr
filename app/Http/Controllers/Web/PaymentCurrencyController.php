<?php

namespace App\Http\Controllers\Web;

use App\Helpers\PaymentCurrencyHelper;
use App\Http\Controllers\Controller;
use App\Repositories\PaymentCurrencyRepository;
use Exception;
use Illuminate\Http\Request;


class PaymentCurrencyController extends Controller
{

    private $view = 'admin.payrollSetting.paymentCurrency.';

    public function __construct(public PaymentCurrencyRepository $paymentCurrencyRepo)
    {
    }

    public function index()
    {
        try{
            $select = ['id','code'];
            $currencyDetail = $this->paymentCurrencyRepo->findPayrollCurrency($select);
            return view($this->view . 'index',compact('currencyDetail'));
        }catch(\Exception $e){
            return redirect()->back()->with('danger', $e->getMessage());
        }
    }

    public function updateOrSetPaymentCurrency(Request $request)
    {
        try{
            $currencyId = $request->get('currency');
            $currencies = collect( PaymentCurrencyHelper::CURRENCY_DETAIL);
            $currencyData = $currencies->firstWhere('id', $currencyId);
            $currencyDetail = $this->paymentCurrencyRepo->findPayrollCurrency();
            $this->paymentCurrencyRepo->updateOrCreatePaymentCurrency($currencyDetail,$currencyData);
            return redirect()
                ->back()
                ->with('success',__('message.currency_update'));
        }catch(Exception $exception){
            return redirect()->back()
                ->with('danger', $exception->getMessage())
                ->withInput();
        }
    }
}
