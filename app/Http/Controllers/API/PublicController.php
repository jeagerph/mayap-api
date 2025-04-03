<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Account;
use App\Models\UserVerification;

use App\Http\Requests\PublicRequest\ForgotPasswordRequest;
use App\Http\Requests\PublicRequest\ValidatePasswordRequest;

use App\Http\Repositories\Base\UserVerificationRepository;
use App\Http\Repositories\Base\SystemSettingRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\CompanySmsTransactionRepository;
use App\Models\Beneficiary;
use App\Models\BeneficiaryIdentification;
use App\Models\Slug;

use App\Traits\MeSender;

class PublicController extends Controller
{
    use MeSender;

    private $userVerificationRepository;
    private $systemSettingRepository;
    private $companyRepository;
    private $smsTransactionRepository;

    public function __construct()
    {
        $this->userVerificationRepository = new UserVerificationRepository;
        $this->systemSettingRepository = new SystemSettingRepository;
        $this->companyRepository = new CompanyRepository;
        $this->smsTransactionRepository = new CompanySmsTransactionRepository;
    }

    public function viewIdentification(Request $request, $code)
    {
      
        $model = Beneficiary::findCodeOrDie($code);

        // if ($model->slug_type != 'App\\Models\\BeneficiaryIdentification')
        //     return abort(404, 'Invalid ID code.');


        $identification = $model;

        return $identification->toArrayPublicDocumentRelated();
    }

    public function forgotPassword(Request $request, ForgotPasswordRequest $formRequest)
    {
        $request->merge([
            'public-forgot-password-related' => true
        ]);

        $account = Account::where('mobile_number', $formRequest->input('mobile_number'))
                            ->where('account_type', 2)
                            ->first();

        $company = $account->companyAccount->company;

        $user = $account->user;

        $this->systemSettingRepository->checkSmsServiceStatus();

        $this->companyRepository->checkSmsServiceStatus($company);

        $userVerification = $user->verifications()->latest()->first();

        if (!$userVerification):

            $userVerification = $user->verifications()->save(
                $this->userVerificationRepository->new([
                    'action' => 'forgot-password',
                ])
            );
        endif;

        $message = "Hi " . $account->full_name . "," . PHP_EOL;
        $message .= "Your reset code is " . $userVerification->code . '.';

        $newSmsTransaction = $this->smsTransactionRepository->storeAndSendTransaction(
            [
                'transaction_date' => now()->format('Y-m-d'),
                'transaction_type' => 1,
                'sms_type' => 2,
                'mobile_number' => $account->mobile_number,
                'message' => $message,
                'source' => 'RESET PASSWORD',
                'scheduled_date' => null,
                'scheduled_time' => null,
            ],
            $company,
        );

        $smsRecipient = $newSmsTransaction->smsRecipients()->latest()->first();

        if ($smsRecipient->status == 2):
            return response([
                'full_name' => $account->full_name,
                'token' => $userVerification->token,
                'mobile_number' => $account->mobile_number
            ], 200);
        endif;

        return abort(403, 'Your reset code was not sent due to some reason. Kindly contact your System Provider to confirm.');
    }

    public function resetPassword(Request $request, ValidatePasswordRequest $formRequest)
    {
        $request->merge([
            'public-forgot-password-related' => true
        ]);

        $verification = UserVerification::where('token', $formRequest->input('token'))->first();

        if (!$verification) return abort(403, 'Token not found.');

        if ($verification->code != $formRequest->input('code'))
            return response([
                'errors' => [
                    'code' => ['Code is not valid.']
                ]
            ], 422);

        $user = $verification->user;

        $newpass = $formRequest->input('password');
        
        $user->update([
            'password' => bcrypt($newpass),
            'updated_by' => 1
        ]);

        $verification->update([
            'token' => 'DELETED',
            'updated_by' => 1,
            'deleted_at' => now(),
        ]);

        return response([
            'message' => 'Your password is now reset. You may now sign in your new password.'
        ], 200);
    }
}
