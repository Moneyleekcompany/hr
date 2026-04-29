<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class PayslipMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $payrollData;
    public $currency;
    public $companyLogoPath;
    public $numberToWords;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payrollData, $currency, $companyLogoPath, $numberToWords)
    {
        $this->payrollData = $payrollData;
        $this->currency = $currency;
        $this->companyLogoPath = $companyLogoPath;
        $this->numberToWords = $numberToWords;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // توليد ملف الـ PDF من نفس واجهة الطباعة
        $pdf = Pdf::loadView('admin.payroll.employeeSalary.print_payslip', [
            'payrollData' => $this->payrollData,
            'currency' => $this->currency,
            'companyLogoPath' => $this->companyLogoPath,
            'numberToWords' => $this->numberToWords,
        ]);

        $payslipMonth = $this->payrollData->payslip_month;
        $payslipYear = $this->payrollData->payslip_year;

        return $this->subject("قسيمة راتب شهر {$payslipMonth}-{$payslipYear}")
                    ->view('emails.payslip') // واجهة البريد الإلكتروني الجديدة
                    ->attachData($pdf->output(), "payslip-{$payslipMonth}-{$payslipYear}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
    }
}