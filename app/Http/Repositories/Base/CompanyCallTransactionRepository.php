<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use App\Models\Slug;
use App\Models\CompanyCallTransaction;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\CompanyCallCreditRepository;
use App\Http\Repositories\Base\CompanyRepository;

class CompanyCallTransactionRepository
{
    public function __construct()
    {
        $this->slugRepository = new SlugRepository;
        $this->companyRepository = new CompanyRepository;
        $this->callCreditRepository = new CompanyCallCreditRepository;
    }

    public function new($data, $company)
    {
        return new CompanyCallTransaction([
            'code' => self::generateCode($data['transaction_date'], $company),
            'transaction_date' => $data['transaction_date'],
            'amount' => $data['amount'],
            'call_duration' => $data['call_duration'],
            'call_sid' => $data['call_sid'],
            'call_status' => $data['call_status'],
            'recording_sid' => $data['recording_sid'],
            'recording_duration' => $data['recording_duration'],
            'recording_url' => $data['recording_url'],
            'mobile_number' => $data['mobile_number'],
            'status' => $data['status'],
            'source' => $data['source'] ?: null,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function store($data, $company)
    {
        $newTransaction = $company->callTransactions()->save(
            $this->new($data, $company)
        );

        $newTransaction->slug()->save(
            $this->slugRepository->new(
                $newTransaction->mobile_number . ' Call Transaction'
            )
        );

        // $company->callCredits()->save(
        //     $this->callCreditRepository->new([
        //         'credit_date' => $newTransaction->transaction_date,
        //         'amount' => $newTransaction->amount,
        //         'credit_mode' => 2,
        //         'remarks' => $newTransaction->code,
        //     ], $company)
        // );
        
        // $this->companyRepository->refreshCallCreditAmount($company);

        return $newTransaction;
    }

    public function generateCode($date, $company)
    {
        $callTransaction = $company->callTransactions()->whereYear('transaction_date', $date)->latest()->first();

        $callTransactionCode = 'CALLTRNSCT-';
        $callTransactionCode .= customDateFormat($date, 'MMYYYY') . leadingZeros($company->id) . '-';
        $callTransactionCode .= self::formCallTransactionCount($callTransaction);

        return $callTransactionCode;
    }

    public function formCallTransactionCount($callTransaction)
    {
        if (!$callTransaction) return leadingZeros(1, $strType = 's', $padChar = 0, $padLength = 3);

        $callTransactionCode = $callTransaction->code;

        $arrCallTransactionCode = explode('-', $callTransactionCode);

        $currentCallTransactionCode = (int) $arrCallTransactionCode[2];

        return leadingZeros($currentCallTransactionCode+1, $strType = 's', $padChar = 0, $padLength = 3);
    }
}
?>