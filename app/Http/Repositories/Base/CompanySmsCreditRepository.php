<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;

use App\Models\CompanySmsCredit;
use App\Models\CompanySmsTransaction;
use App\Models\CompanySmsRecipient;

use App\Http\Repositories\Base\SlugRepository;

class CompanySmsCreditRepository
{
    public function new($data, $company)
    {
        return new CompanySmsCredit([
            'code' => self::generateCode($data['credit_date'], $company),
            'credit_date' => $data['credit_date'],
            'amount' => $data['amount'],
            'credit_mode' => $data['credit_mode'],
            'remarks' => $data['remarks'],
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function newTransaction($company, $transaction)
    {
        $smsTransaction = CompanySmsTransaction::find($transaction->id);

        $remarks = $smsTransaction->code . PHP_EOL;
        $remarks .= 'CREDIT / SMS: ' . $smsTransaction->credit_per_sent . PHP_EOL;
        $remarks .= 'TOTAL RECIPIENT: ' . $smsTransaction->smsRecipients->count() . PHP_EOL;
        $remarks .= 'SENT SMS: ' . $smsTransaction->successRecipientsCount() . PHP_EOL;
        $remarks .= 'FAILED SMS: ' . $smsTransaction->failedRecipientsCount() . PHP_EOL;
        $remarks .= 'PENDING SMS: ' . $smsTransaction->pendingRecipientsCount() . PHP_EOL;
        $remarks .= 'MESSAGE: ' . $smsTransaction->message; 

        $company->smsCredits()->save(
            $this->new([
                'credit_date' => now()->format('Y-m-d'),
                'credit_mode' => 2,
                'amount' => $smsTransaction->amount,
                'remarks' => $remarks,
            ], $company)
        );
    }

    public function newRecipient($company, $recipient)
    {
        $smsRecipient = CompanySmsRecipient::find($recipient->id);

        $smsTransaction = $smsRecipient->smsTransaction;

        $remarks = 'RECIPIENT: ' . $recipient->mobile_number . PHP_EOL;
        $remarks .= 'SMS TRANSACTION: ' . $smsTransaction->code . PHP_EOL;
        $remarks .= 'CREDIT / SMS: ' . $smsTransaction->credit_per_sent . PHP_EOL;
        $remarks .= 'STATUS: ' . $smsRecipient->statuses[$smsRecipient->status] . PHP_EOL;
        $remarks .= 'MESSAGE: ' . $smsTransaction->message; 

        $company->smsCredits()->save(
            $this->new([
                'credit_date' => now()->format('Y-m-d'),
                'credit_mode' => 2,
                'amount' => $smsTransaction->credit_per_sent,
                'remarks' => $remarks,
            ], $company)
        );
    }

    public function generateCode($date, $company)
    {
        $credit = $company->smsCredits()->whereYear('credit_date', $date)->latest()->first();

        $creditCode = 'SMSCRDT-';
        $creditCode .= customDateFormat($date, 'MMYYYY') . leadingZeros($company->id) . '-';
        $creditCode .= self::formSmsCreditCode($credit);

        return $creditCode;
    }

    public function formSmsCreditCode($credit)
    {
        if (!$credit) return leadingZeros(1, $strType = 's', $padChar = 0, $padLength = 3);

        $creditCode = $credit->code;

        $arrCreditCode = explode('-', $creditCode);

        $currentCreditCode = (int) $arrCreditCode[2];

        return leadingZeros($currentCreditCode+1, $strType = 's', $padChar = 0, $padLength = 3);
    }
}
?>