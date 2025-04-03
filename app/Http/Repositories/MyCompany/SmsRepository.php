<?php

namespace App\Http\Repositories\MyCompany;

use Illuminate\Support\Facades\Auth;

use App\Models\Slug;
use App\Models\Company;
use App\Models\CompanySmsTransaction;
use App\Models\CompanySmsRecipient;

use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\CompanySmsTransactionRepository;
use App\Http\Repositories\Base\CompanySmsRecipientRepository;
use App\Http\Repositories\Base\CompanySmsSettingRepository;
use App\Http\Repositories\Base\SystemSettingRepository;
use App\Http\Repositories\Base\SlugRepository;

class SmsRepository
{
    public function __construct()
    {
        $this->slugRepository = new SlugRepository;
        $this->companyRepository = new CompanyRepository;
        $this->systemSettingRepository = new SystemSettingRepository;
        $this->smsTransactionRepository = new CompanySmsTransactionRepository;
        $this->smsRecipientRepository = new CompanySmsRecipientRepository;
        $this->smsSettingRepository = new CompanySmsSettingRepository;
    }

    public function sendSms($request)
    {
        $user = Auth::user();

        $company = $user->company();

        $this->systemSettingRepository->checkSmsServiceStatus();

        if ($request->input('sms_type') == 2):

            $this->companyRepository->checkSmsServiceStatus($company);

        elseif ($request->input('sms_type') == 1):

            $this->companyRepository->checkDiafaanSmsServiceStatus($company);

        endif;

        $this->smsSettingRepository->checkCreditBalance(
            $company,
            1,
            $request->input('message')
        );

        $newSmsTransaction = $company->smsTransactions()->save(
            $this->smsTransactionRepository->new($request, $company)
        );

        $newSmsTransaction->slug()->save(
            $this->slugRepository->new(
                $newSmsTransaction->code . ' SMS Transaction'
            )
        );

        $newSmsRecipient = $newSmsTransaction->smsRecipients()->save(
            $this->smsRecipientRepository->new([
                'mobile_number' => $request->input('mobile_number'),
                'message' => $newSmsTransaction->message,
            ])
        );

        $this->smsTransactionRepository->sendSmsTransaction($newSmsTransaction);

        $updatedTransaction = CompanySmsTransaction::find($newSmsTransaction->id);
        $updatedRecipient = CompanySmsRecipient::find($newSmsRecipient->id);

        if ($updatedRecipient->status == 2):
            return response([
                'code' => $updatedTransaction->code,
                'possible_amount' => $updatedTransaction->amount,
            ],
            201);
        endif;

        return abort(403, 'Your message was not sent to the recipient due to some reason. Kindly contact your System Provider to confirm.');
    }

    public function showSmsTransaction($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $smsTransaction = $model->slug;

        $company = Auth::user()->company();

        $this->companyRepository->isSmsTransactionRelated($company, $smsTransaction->id);

        return $smsTransaction->toArrayMyCompanySmsTransactionsRelated();
    }

    public function updateSmsTransactionMessage($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $smsTransaction = $model->slug;

        $company = Auth::user()->company();

        $this->companyRepository->isSmsTransactionRelated($company, $smsTransaction->id);

        if($smsTransaction->status != 1) return abort(403, 'Forbidden: You cannot update a transaction on not PENDING status.');

        $smsTransaction->update([
            'message' => $request->input('message'),
            'updated_by' => Auth::id()
        ]);

        $this->smsTransactionRepository->computeCreditPerSent($smsTransaction);

        return (CompanySmsTransaction::find($smsTransaction->id))->toArrayMyCompanySmsTransactionsRelated();
    }

    public function cancelSmsTransaction($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $smsTransaction = $model->slug;

        $company = Auth::user()->company();

        $this->companyRepository->isSmsTransactionRelated($company, $smsTransaction->id);

        $smsTransaction->update([
            'status' => 3,
            'updated_by' => Auth::id()
        ]);

        return (CompanySmsTransaction::find($smsTransaction->id))->toArrayMyCompanySmsTransactionsRelated();
    }

    public function destroySmsTransaction($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $smsTransaction = $model->slug;

        $company = Auth::user()->company();

        $this->companyRepository->isSmsTransactionRelated($company, $smsTransaction->id);

        if ($smsTransaction->status != 1) return abort(403, 'Forbidden: You cannot delete a transaction on not PENDING status of transaction.');

        $smsTransaction->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }

    public function showSmsRecipients($request, $code)
    {
        $model = Slug::findCodeOrDie($code);

        $smsTransaction = $model->slug;

        $company = Auth::user()->company();

        $this->companyRepository->isSmsTransactionRelated($company, $smsTransaction->id);

        $model = new CompanySmsRecipient;

        return $model->build();
    }

    public function updateSmsRecipient($request, $code, $recipientId)
    {
        $model = Slug::findCodeOrDie($code);

        $smsTransaction = $model->slug;

        $company = Auth::user()->company();

        $this->companyRepository->isSmsTransactionRelated($company, $smsTransaction->id);

        $recipient = $this->smsTransactionRepository->isRecipientRelated($smsTransaction, $recipientId);

        if ($smsTransaction->status != 2) return abort(403, 'Forbidden: You cannot update a recipient on APPROVED status of transaction.');

        if ($recipient->status == 2) return abort(403, 'Forbidden: You cannot update a recipient on a SENT status.');

        $recipient->update([
            'mobile_number' => $request->input('mobile_number'),
            'status' => 1,
            'updated_by' => Auth::id()
        ]);

        return (CompanySmsRecipient::find($recipient->id))->toArrayMyCompanySmsTransactionRecipientsRelated();
    }

    public function sendSmsRecipient($request, $code, $recipientId)
    {
        $company = Auth::user()->company();

        $model = Slug::findCodeOrDie($code);

        $smsTransaction = $model->slug;

        $recipient = $this->smsTransactionRepository->isRecipientRelated($smsTransaction, $recipientId);

        if ($smsTransaction->status != 2) return abort(403, 'Forbidden: You cannot send a recipient on not APPROVED status of transaction.');

        if ($recipient->status == 2) return abort(403, 'Forbidden: You cannot send a recipient on a SENT status.');

        $this->companyRepository->checkCreditBalance($company, 1, $smsTransaction->message);

        if ($smsTransaction->registered_domain == 2):

            $this->smsTransactionRepository->sendKabataanSmsRecipient($recipient);
        else:

            $this->smsTransactionRepository->sendSmsRecipient($recipient);
        endif;

        return (CompanySmsRecipient::find($recipient->id))->toArrayMyCompanySmsTransactionRecipientsRelated();
    }

    public function destroySmsRecipient($request, $code, $recipientId)
    {
        $model = Slug::findCodeOrDie($code);

        $smsTransaction = $model->slug;

        $recipient = $this->smsTransactionRepository->isRecipientRelated($smsTransaction, $recipientId);

        if ($smsTransaction->status != 1) return abort(403, 'Forbidden: You cannot delete a recipient on not PENDING status of transaction.');

        if ($recipient->status == 2) return abort(403, 'Forbidden: You cannot send a recipient on a SENT status.');

        $recipient->delete();

        return response([
            'message' => 'Data deleted successfully.'
        ], 200);
    }
}
?>