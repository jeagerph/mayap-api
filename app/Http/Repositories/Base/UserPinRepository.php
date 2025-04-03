<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

use App\Models\UserPin;

// use App\Http\Repositories\Base\CompanySmsTransactionRepository;
// use App\Http\Repositories\Base\CompanySmsRecipientRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\SystemSettingRepository;
use App\Http\Repositories\Base\SlugRepository;

class UserPinRepository
{
    public function __construct()
    {
        // $this->smsTransactionRepository = new CompanySmsTransactionRepository;
        // $this->smsRecipientRepository = new CompanySmsRecipientRepository;
        $this->slugRepository = new SlugRepository;
        $this->companyRepository = new CompanyRepository;
        $this->systemSettingRepository = new SystemSettingRepository;
    }

    public function generate($user)
    {
        $pin = $user->pins()->latest()->first();

        if (!$pin):
            $pin = $user->pins()->save(
                new UserPin([
                    'token' => Str::random(32),
                    'otp' => randomNumbers(6),
                    'created_by' => 1
                ])
            );
        endif;

        return $pin;
    }

    public function sendOtp($user)
    {
        // $account = $user->account;
        // $mobileNumber = $account->mobile_number;

        // $company = $user->company();

        // $this->systemSettingRepository->checkSmsServiceStatus();

        // $this->companyRepository->checkSmsServiceStatus($company);

        // $pin = $user->pins()->latest()->first();

        // $message = 'Hi ' . $account->full_name . PHP_EOL;
        // $message .= 'Here is your OTP: ' . $pin->otp;

        // $this->companyRepository->checkCreditBalance($company, 1, $message);

        // $newSmsTransaction = $company->smsTransactions()->save(
        //     $this->smsTransactionRepository->new([
        //         'message' => $message,
        //         'sms_type' => 2,
        //         'transaction_date' => now()->format('Y-m-d'),
        //         'transaction_type' => 1,
        //         'scheduled_date' => null,
        //         'scheduled_time' => null,
        //         'source' => 'LOGIN OTP',
        //     ], $company)
        // );

        // $newSmsTransaction->slug()->save(
        //     $this->slugRepository->new(
        //         $newSmsTransaction->code . ' SMS Transaction'
        //     )
        // );

        // $newSmsRecipient = $newSmsTransaction->smsRecipients()->save(
        //     $this->smsRecipientRepository->new([
        //         'mobile_number' => $mobileNumber,
        //         'message' => $newSmsTransaction->message,
        //     ])
        // );

        // $this->smsTransactionRepository->sendSmsTransaction($newSmsTransaction, 'LOGIN OTP');
    }
}
?>