<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanyCallCredit;

use App\Http\Repositories\Base\SlugRepository;

class CompanyCallCreditRepository
{
    public function new($data, $company)
    {
        return new CompanyCallCredit([
            'code' => self::generateCode($data['credit_date'], $company),
            'credit_date' => $data['credit_date'],
            'amount' => $data['amount'],
            'credit_mode' => $data['credit_mode'],
            'remarks' => $data['remarks'],
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function generateCode($date, $company)
    {
        $credit = $company->callCredits()->whereYear('credit_date', $date)->latest()->first();

        $creditCode = 'CALLCRDT-';
        $creditCode .= customDateFormat($date, 'MMYYYY') . leadingZeros($company->id) . '-';
        $creditCode .= self::formCallCreditCode($credit);

        return $creditCode;
    }

    public function formCallCreditCode($credit)
    {
        if (!$credit) return leadingZeros(1, $strType = 's', $padChar = 0, $padLength = 3);

        $creditCode = $credit->code;

        $arrCreditCode = explode('-', $creditCode);

        $currentCreditCode = (int) $arrCreditCode[2];

        return leadingZeros($currentCreditCode+1, $strType = 's', $padChar = 0, $padLength = 3);
    }
}
?>