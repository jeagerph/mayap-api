<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanySmsTransaction;

use App\Http\Repositories\MyCompany\SmsRepository as Repository;

use App\Http\Requests\MyCompany\Sms\SendSmsRequest;
use App\Http\Requests\MyCompany\Sms\UpdateSmsRecipientRequest;
use App\Http\Requests\MyCompany\Sms\UpdateSmsTransactionMessageRequest;

class SmsController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function sendSms(Request $request, SendSmsRequest $formRequest)
    {
        $request->merge([
            'my-company-sms-transactions-related' => true
        ]);

        return $this->repository->sendSms($formRequest);
    }

    public function showSmsTransactions(Request $request)
    {
        $company = Auth::user()->company();

        $filters = [
            'companyCode' => $company->slug->code,
        ];

        $sorts = [
            'created' => 'desc',
        ];

        $request->merge([
            'my-company-sms-transactions-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts)
        ]);

        $model = new CompanySmsTransaction;

        return $model->build();
    }

    public function showSmsTransaction(Request $request, $code)
    {
        $request->merge([
            'my-company-sms-transactions-related' => true
        ]);

        return $this->repository->showSmsTransaction($request, $code);
    }

    public function updateSmsTransactionMessage(Request $request, UpdateSmsTransactionMessageRequest $formRequest, $code)
    {
        $request->merge([
            'my-company-sms-transactions-related' => true
        ]);

        return $this->repository->updateSmsTransactionMessage($formRequest, $code);
    }

    public function cancelSmsTransaction(Request $request, $code)
    {
        $request->merge([
            'my-company-sms-transactions-related' => true
        ]);

        return $this->repository->cancelSmsTransaction($request, $code);
    }

    public function destroySmsTransaction(Request $request, $code)
    {
        $request->merge([
            'my-company-sms-transaction-deletion' => true
        ]);

        return $this->repository->destroySmsTransaction($request, $code);
    }

    public function showSmsRecipients(Request $request, $code)
    {
        $filters = [
            'smsTransactionCode' => $code,
        ];

        $sorts = [
            'status' => 'asc',
            'created' => 'desc',
        ];

        $request->merge([
            'my-company-sms-transaction-recipients-related' => true,
            'filter' => $this->handleQueries('filter', $request, $filters),
            'sort' => $this->handleQueries('sort', $request, $sorts),
        ]);
        
        return $this->repository->showSmsRecipients($request, $code);
    }

    public function updateSmsRecipient(Request $request, UpdateSmsRecipientRequest $formRequest, $code, $recipientId)
    {
        $request->merge([
            'my-company-sms-transaction-recipients-related' => true
        ]);

        return $this->repository->updateSmsRecipient($formRequest, $code, $recipientId);
    }

    public function sendSmsRecipient(Request $request, $code, $recipientId)
    {
        $request->merge([
            'my-company-sms-transaction-recipients-related' => true
        ]);

        return $this->repository->sendSmsRecipient($request, $code, $recipientId);
    }

    public function destroySmsRecipient(Request $request, $code, $recipientId)
    {
        $request->merge([
            'my-company-sms-transaction-recipient-deletion' => true
        ]);

        return $this->repository->destroySmsRecipient($request, $code, $recipientId);
    }
}
