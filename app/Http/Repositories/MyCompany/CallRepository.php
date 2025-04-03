<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use Twilio\Jwt\AccessToken;
use Twilio\Jwt\Grants\VoiceGrant;
use Twilio\TwiML\VoiceResponse;
use Twilio\Rest\Client;

use App\Models\Slug;
use App\Models\Company;
use App\Models\CompanyCallTransaction;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\SystemSettingRepository;
use App\Http\Repositories\Base\CompanyCallTransactionRepository;
use App\Http\Repositories\Base\BeneficiaryCallRepository;

use App\Traits\MeSender;

class CallRepository
{
    use MeSender;

    public function __construct()
    {
        $this->slugRepository = new SlugRepository;
        $this->companyRepository = new CompanyRepository;
        $this->systemSettingRepository = new SystemSettingRepository;
        $this->callTransactionRepository = new CompanyCallTransactionRepository;
        $this->beneficiaryCallRepository = new BeneficiaryCallRepository;
    }

    public function showSetting($request)
    {
        $user = Auth::user();

        $company = $user->company();

        return $company->callSetting->toArrayMyCompanyCallSettingRelated();
    }

    public function generateToken($request)
    {
        $user = Auth::user();

        $company = $user->company();

        $setting = $company->callSetting;

        $identity = $company->name . '-' . $user->account->full_name;

        $token = new AccessToken(
            $setting->account_sid,
            $setting->api_key,
            $setting->api_secret,
            3600,
            $setting->app_sid,
            $identity
        );

        $voiceGrant = new VoiceGrant();
        $voiceGrant->setOutgoingApplicationSid($setting->app_sid);

        // Optional: add to allow incoming calls
        $voiceGrant->setIncomingAllow(true);

        // Add grant to token
        $token->addGrant($voiceGrant);

        // render token to string
        return $token->toJWT();
    }

    public function storeCallTransaction($request)
    {
        $user = Auth::user();

        $company = $user->company();

        $newTransaction = $this->callTransactionRepository->store([
            'transaction_date' => $request->input('transaction_date'),
                'mobile_number' => $request->input('mobile_number'),
                'amount' => 0,
                'recording_url' => null,
                'call_duration' => 0,
                'call_sid' => null,
                'call_status' => null,
                'recording_sid' => null,
                'recording_url' => null,
                'recording_duration' => 0,
                'recording_status' => null,
                'status' => 1,
                'source' => $request->input('source'),
        ], $company);

        return $newTransaction;
    }

    public function updateCallTransaction($request, $code)
    {
        $user = Auth::user();

        $company = $user->company();

        $model = Slug::findCodeOrDie($code);

        $callTransaction = $model->slug;

        $this->companyRepository->isCallTransactionRelated($company, $callTransaction->id);

        $callTransaction->update([
            'call_sid' => $request->input('call_sid'),
            'updated_by' => Auth::id() ?: 1
        ]);

        if ($request->input('source') == 'beneficiary'):

            $beneficiaryCall = $callTransaction->beneficiaryCall;

            if (!$beneficiaryCall):
                $beneficiarySlug = Slug::where('code', $request->input('beneficiaryCode'))->first();

                if ($beneficiarySlug):

                    $beneficiary = $beneficiarySlug->slug;

                    $beneficiary->calls()->save(
                        $this->beneficiaryCallRepository->new([
                            'call_date' => $callTransaction->transaction_date,
                            'mobile_number' => $callTransaction->mobile_number,
                            'company_call_transaction_id' => $callTransaction->id,
                            'status' => 2
                        ], $company)
                    );
                endif;
            else:

                $beneficiaryCall->update([
                    'status' => $callTransaction->status,
                    'updated_by' => Auth::id()?: 1
                ]);

            endif;

            

        endif;

        return (CompanyCallTransaction::find($callTransaction->id))->toArrayMyCompanyCallTransactionsRelated();
    }

    public function showCallTransactionRecording($request, $code)
    {
        $user = Auth::user();

        $company = $user->company();

        $model = Slug::findCodeOrDie($code);

        $callTransaction = $model->slug;

        $this->companyRepository->isCallTransactionRelated($company, $callTransaction->id);

        $setting = $company->callSetting;

        $client = new \Twilio\Rest\Client($setting->account_sid, $setting->auth_token);

        return ($client->recordings($callTransaction->recording_sid)->fetch())->mediaUrl . '.mp3';
    }

    // public function call($request)
    // {
    //     $user = Auth::user();

    //     $company = $user->company();

    //     $this->systemSettingRepository->checkCallServiceStatus();

    //     $this->companyRepository->checkSmsServiceStatus($company);

    //     $setting = $company->callSetting;

    //     if (env('APP_ENV') == 'production'):

    //         $this->fireCall(
    //             $setting->account_sid,
    //             $setting->auth_token,
    //             $setting->auth_url,
    //             $setting->phone_no,
    //             formatPhoneNumber($request->input('mobile_number'))
    //         );

    //     endif;

    //     return response([
    //         'message' => 'Call initiated.',
    //         'mobile_number' => formatPhoneNumber($request->input('mobile_number'))
    //     ], 200);
    // }
}
?>