<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Facades\Auth;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use App\Models\Slug;
use App\Models\CompanySmsTransaction;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\CompanySmsCreditRepository;
use App\Http\Repositories\Base\CompanySmsRecipientRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\SystemSettingRepository;

use App\Traits\MeSender;
use App\Traits\MessageTemplate;

class CompanySmsTransactionRepository
{
    use MeSender, MessageTemplate;

    public function __construct()
    {
        $this->slugRepository = new SlugRepository;
        $this->companyRepository = new CompanyRepository;
        $this->smsCreditRepository = new CompanySmsCreditRepository;
        $this->smsRecipientRepository = new CompanySmsRecipientRepository;
        $this->systemSettingRepository = new SystemSettingRepository;
    }

    public function new($data, $company, $mobile = false)
    {   
        $smsSetting = $company->smsSetting;
        $smsHeaderName = $smsSetting->header_name;
        $smsFooterName = $smsSetting->footer_name;

        $smsMessage = formSmsMessage(
            $data['message'],
            $smsHeaderName,
            $smsFooterName,
            $mobile
        );

        $chargePerSms = $data['sms_type'] == 1
            ? ($smsSetting->credit_per_regular_sms ?: .30)
            : ($smsSetting->credit_per_branding_sms ?: .50);

        return new CompanySmsTransaction([
            'code' => self::generateCode($data['transaction_date'], $company),
            'amount' => 0.00,
            'message' => $smsMessage,
            'max_char_per_sms' => $smsSetting->max_char_per_sms,
            'credit_per_sent' => computeMessageCreditCharge(
                $smsMessage,
                $chargePerSms,
                $smsSetting->max_char_per_sms
            ),
            'sms_type' => $data['sms_type'],
            'transaction_date' => $data['transaction_date'],
            'transaction_type' => $data['transaction_type'],
            'scheduled_date' => $data['scheduled_date'] ?: null,
            'scheduled_time' => $data['scheduled_time'] ?: null,
            'status' => 1,
            'source' => $data['source'] ?: null,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    public function newResetPassword($data, $company)
    {   
        $smsSetting = $company->smsSetting;
        $transactionYear = $company->smsTransaction()->whereYear('transaction_date', now()->format('Y'))->first();

        $chargePerSms = $data['sms_type'] == 1
            ? ($smsSetting->regular_sms ?: .30)
            : ($smsSetting->branding_sms ?: .50);

        return new SmsCredit([
            'code' => self::generateCode($data['transaction_date'], $company),
            'transaction_year_id' => $transactionYear->id,
            'amount' => 0,
            'message' => $data['message'],
            'sms_type' => $data['sms_type'],
            'transaction_date' => $data['transaction_date'],
            'transaction_type' => $data['transaction_type'],
            'max_char_per_sms' => $smsSetting->max_char_per_sms,
            'credit_per_sent' => computeMessageCreditCharge(
                $data['message'],
                $chargePerSms,
                $smsSetting->max_char_per_sms
            ),
            'scheduled_date' => $data['scheduled_date'] ?: null,
            'scheduled_time' => $data['scheduled_time'] ?: null,
            'status' => 1,
            'source' => $data['source'] ?: null,
            'created_by' => Auth::id() ?: 1
        ]);
    }

    // NOT IN USE
    public function storeAndSend($company, $mobileNumber, $message, $source)
    {
        $newSmsTransaction = $company->smsTransactions()->save(
            self::new([
                'message' => $message,
                'sms_type' => 2,
                'transaction_date' => now()->format('Y-m-d'),
                'transaction_type' => 1,
                'scheduled_date' => null,
                'scheduled_time' => null,
                'source' => $source,
            ], $company)
        );

        $newSmsTransaction->slug()->save(
            $this->slugRepository->new(
                $newSmsTransaction->code . ' SMS Transaction'
            )
        );

        $newSmsRecipient = $newSmsTransaction->smsRecipients()->save(
            $this->smsRecipientRepository->new([
                'mobile_number' => $mobileNumber,
                'message' => $newSmsTransaction->message,
            ])
        );

        self::sendSmsTransaction($newSmsTransaction);
    }

    public function sendSmsTransaction($smsTransaction, $source = NULL)
    {
        $recipients = $smsTransaction->smsRecipients()->where('status', 1)->get();

        $company = $smsTransaction->company;

        $smsSetting = $company->smsSetting;

        $sentMobileNumbers = [];
        $failedMobileNumbers = [];

        $creditBalance = $smsSetting->sms_credit;

        $totalCreditBalance = $creditBalance + $smsSetting->credit_threshold;

        $totalCreditCharge = 0;

        foreach($recipients as $recipient):

            if(mobileNumberValidator($recipient->mobile_number)):

                if(!in_array($recipient->mobile_number, $sentMobileNumbers)):

                    if ($totalCreditBalance >= $smsTransaction->credit_per_sent):

                        if ($smsTransaction->sms_type == 2):

                            $response = $this->fireBrandingSmsToRecipient(
                                $recipient,
                                $smsSetting->branding_api_url,
                                $smsSetting->branding_api_code,
                            );

                        else:
                            $response = $this->fireRegularSmsToRecipient(
                                $recipient,
                            );
                        endif;
    
                        if ($response['statusCode'] == 200 && strpos($response['response'], 'successfully') !== false):
    
                            $recipient->update([
                                'status' => 2,
                                'sent_at' => now(),
                                'failure_message' => null,
                                'updated_by' => 1
                            ]);
    
                            $sentMobileNumbers[] = $recipient->mobile_number;

                            $totalCreditBalance -= $smsTransaction->credit_per_sent;
    
                        else:

                            if ($source == 'LOGIN OTP'):

                                $response = $this->fireDiafaanOtp(
                                    $recipient->mobile_number,
                                    $recipient->message,
                                );

                                $recipient->update([
                                    'status' => 2,
                                    'sent_at' => now(),
                                    'failure_message' => null,
                                    'updated_by' => 1
                                ]);
        
                                $sentMobileNumbers[] = $recipient->mobile_number;
    
                                $totalCreditBalance -= $smsTransaction->credit_per_sent;

                            else:

                                $recipient->update([
                                    'status' => 3,
                                    'failure_message' => 'Failure to send SMS.',
                                    'updated_by' => 1
                                ]);
        
                                $failedMobileNumbers[] = $recipient->mobile_number;

                            endif;
    
                            
                            
                        endif;

                    else:
                        $recipient->update([
                            'status' => 3,
                            'failure_message' => 'Insufficient credit balance.',
                            'updated_by' => 1
                        ]);
        
                        $failedMobileNumbers[] = $recipient->mobile_number;

                    endif;
                else:

                    $recipient->update([
                        'status' => 3,
                        'failure_message' => 'Duplicate mobile number',
                        'updated_by' => 1
                    ]);
    
                    $failedMobileNumbers[] = $recipient->mobile_number;

                endif;

            else:

                $recipient->update([
                    'status' => 3,
                    'failure_message' => 'Invalid format of mobile number',
                    'updated_by' => 1
                ]);

                $failedMobileNumbers[] = $recipient->mobile_number;
            endif;

        endforeach;

        $this->computeTransactionAmount($smsTransaction);

        $this->approveTransactionStatus($smsTransaction);

        $this->smsCreditRepository->newTransaction($company, $smsTransaction);

        $this->companyRepository->refreshCreditAmount($company);

        $log = new Logger('company_sms_transactions_logs');
        $log->pushHandler(new StreamHandler(storage_path('logs/company_sms_transactions_logs.log')), Logger::INFO);
        $log->info('company_sms_transactions_logs', [
            'api_url' => $smsSetting->branding_api_url,
            'api_key' => $smsSetting->branding_api_code,
            'code' => $smsTransaction->code,
            'message' => $smsTransaction->message,
            'sent_mobile_numbers' => count($sentMobileNumbers),
            'failed_mobile_numbers' => count($failedMobileNumbers),
        ]);
    }

    public function sendSmsRecipient($recipient)
    {
        $smsTransaction = $recipient->smsTransaction;

        $company = $smsTransaction->company;

        $smsSetting = $company->smsSetting;

        $sentMobileNumbers = [];
        $failedMobileNumbers = [];

        $creditBalance = $smsSetting->sms_credit;

        $totalCreditBalance = $creditBalance + $smsSetting->credit_threshold;

        $totalCreditCharge = 0;

        if(mobileNumberValidator($recipient->mobile_number)):

            if(!in_array($recipient->mobile_number, $sentMobileNumbers)):

                if ($totalCreditBalance >= $smsTransaction->credit_per_sent):

                    if ($smsTransaction->sms_type == 2):

                        $response = $this->fireBrandingSmsToRecipient(
                            $recipient,
                            $smsSetting->branding_api_url,
                            $smsSetting->branding_api_code,
                        );

                    else:
                        
                        $response = $this->fireRegularSmsToRecipient(
                            $recipient,
                        );
                    endif;
    
                    if ($response['statusCode'] == 200 && strpos($response['response'], 'successfully') !== false):
    
                        $recipient->update([
                            'status' => 2,
                            'sent_at' => now(),
                            'failure_message' => null,
                            'updated_by' => 1
                        ]);
    
                        $sentMobileNumbers[] = $recipient->mobile_number;

                        $totalCreditBalance -= $smsTransaction->credit_per_sent;
    
                    else:
    
                        $recipient->update([
                            'status' => 3,
                            'failure_message' => 'Failure to send SMS.',
                            'updated_by' => 1
                        ]);
    
                        $failedMobileNumbers[] = $recipient->mobile_number;
                        
                    endif;

                else:
                    $recipient->update([
                        'status' => 3,
                        'failure_message' => 'Insufficient credit balance.',
                        'updated_by' => 1
                    ]);
    
                    $failedMobileNumbers[] = $recipient->mobile_number;
                endif;
            
                
            else:

                $recipient->update([
                    'status' => 3,
                    'failure_message' => 'Duplicate mobile number',
                    'updated_by' => 1
                ]);

                $failedMobileNumbers[] = $recipient->mobile_number;

            endif;

        else:

            $recipient->update([
                'status' => 3,
                'failure_message' => 'Invalid format of mobile number',
                'updated_by' => 1
            ]);

            $failedMobileNumbers[] = $recipient->mobile_number;
        endif;

        $this->computeTransactionAmount($smsTransaction);

        $this->smsCreditRepository->newRecipient($company, $recipient);

        $this->companyRepository->refreshCreditAmount($company);


        $log = new Logger('company_sms_transactions_logs');
        $log->pushHandler(new StreamHandler(storage_path('logs/company_sms_transactions_logs.log')), Logger::INFO);
        $log->info('company_sms_transactions_logs', [
            'api_url' => $smsSetting->branding_api_url,
            'api_key' => $smsSetting->branding_api_code,
            'code' => $smsTransaction->code,
            'message' => $smsTransaction->message,
            'sent_mobile_numbers' => count($sentMobileNumbers),
            'failed_mobile_numbers' => count($failedMobileNumbers),
        ]);
    }

    public function storeAndSendTransaction($data, $company, $mobile = false)
    {
        $smsSetting = $company->smsSetting;

        $chargePerSms = $data['sms_type'] == 1
            ? ($smsSetting->credit_per_regular_sms ?: .30)
            : ($smsSetting->credit_per_branding_sms ?: .50);

        $newSmsTransaction = $company->smsTransactions()->save(
            new CompanySmsTransaction([
                'code' => self::generateCode($data['transaction_date'], $company),
                'amount' => 0.00,
                'message' => $data['message'],
                'max_char_per_sms' => $smsSetting->max_char_per_sms,
                'credit_per_sent' => computeMessageCreditCharge(
                    $data['message'],
                    $chargePerSms,
                    $smsSetting->max_char_per_sms
                ),
                'sms_type' => $data['sms_type'],
                'transaction_date' => $data['transaction_date'],
                'transaction_type' => $data['transaction_type'],
                'scheduled_date' => $data['scheduled_date'] ?: null,
                'scheduled_time' => $data['scheduled_time'] ?: null,
                'status' => 1,
                'source' => $data['source'] ?: null,
                'created_by' => Auth::id() ?: 1
            ])
        );

        $newSmsTransaction->slug()->save(
            $this->slugRepository->new(
                $newSmsTransaction->code . ' SMS Transaction'
            )
        );

        $newSmsRecipient = $newSmsTransaction->smsRecipients()->save(
            $this->smsRecipientRepository->new([
                'mobile_number' => $data['mobile_number'],
                'message' => $newSmsTransaction->message,
            ])
        );

        self::sendSmsTransaction($newSmsTransaction);

        return $newSmsTransaction;
    }

    public function sendStatusReportSms($reportDate, $company)
    {
        // Checking of system setting SMS status

        $systemSmsServiceStatus = $this->systemSettingRepository->isSmsServiceActive();

        if (!$systemSmsServiceStatus) return [
            'status' => false,
            'statusMessage' => 'System SMS status is OFF.'
        ];

        // Checking of company SMS status

        $companySmsServiceStatus = $this->companyRepository->isCompanySmsServiceActive($company);

        if (!$companySmsServiceStatus) return [
            'status' => false,
            'statusMessage' => 'Company SMS status is OFF.'
        ];

        $smsSetting = $company->smsSetting;

        if (!$smsSetting->report_status) return [
            'status' => false,
            'statusMessage' => 'Report SMS status is OFF.'
        ];

        $dates = [
            'date' => $reportDate
        ];

        $beneficiaries = $this->companyRepository->beneficiariesTotal($dates, $company);
        $patients = $this->companyRepository->patientsTotal($dates, $company);
        $incentives = $this->companyRepository->incentivesTotal($dates, $company);
        $household = $this->companyRepository->householdTotal($dates, $company);
        $requested = $this->companyRepository->requestedAssistancesTotal($dates, $company);
        $assisted = $this->companyRepository->assistedAssistancesTotal($dates, $company);
        $officers = $this->companyRepository->officersTotal($dates, $company);
        $networks = $this->companyRepository->networksTotal($dates, $company);
        $assistancesByType = $this->companyRepository->assistancesByTypeTotal($dates, $company);
        $householdByBarangay = $this->companyRepository->householdByBarangayTotal($company);
        $householdByPurok = $this->companyRepository->householdByPurokTotal($company);

        $message = $this->reportTemplate([
            'report_date' => (new \Carbon\Carbon($reportDate))->format('M d, Y'),
            'report_time' => now()->format('H:iA'),
            'beneficiaries' => $beneficiaries,
            'requested' => $requested,
            'assisted' => $assisted,
            'officers' => $officers,
            'networks' => $networks,
            'incentives' => $incentives,
            'household' => $household,
            'householdByBarangay' => $householdByBarangay,
            'householdByPurok' => $householdByPurok,
            'assistancesByType' => $assistancesByType,
        ], $smsSetting->report_template);

        $reportMobileNumbers = $smsSetting->report_mobile_numbers
            ? explode(',', $smsSetting->report_mobile_numbers)
            : [];

        $arrSentMobileNumbers = [];
        $arrFailedMobileNumbers = [];

        foreach ($reportMobileNumbers as $mobileNumber):

            if (mobileNumberValidator($mobileNumber)):

                self::storeAndSendTransaction([
                    'transaction_date' => $reportDate,
                    'transaction_type' => 1,
                    'sms_type' => 2,
                    'mobile_number' => $mobileNumber,
                    'message' => $message,
                    'source' => 'DAILY REPORT',
                    'scheduled_date' => null,
                    'scheduled_time' => null,
                ], $company);

                $arrSentMobileNumbers[] = $mobileNumber;
            else:
                $arrFailedMobileNumbers[] = $mobileNumber;
            endif;
        endforeach;

        return [
            'status' => true,
            'statusMessage' => 'Success.',
            'message' => $message,
            'sent_mobile_numbers' => count($arrSentMobileNumbers)
                ? implode(',', $arrSentMobileNumbers)
                : null,
            'failed_mobile_numbers' => count($arrFailedMobileNumbers)
                ? implode(',', $arrFailedMobileNumbers)
                : null,
        ];
    }

    public function sendBirthdaySms($birthDate, $company)
    {
        // Checking of system setting SMS status

        $systemSmsServiceStatus = $this->systemSettingRepository->isSmsServiceActive();

        if (!$systemSmsServiceStatus) return [
            'status' => false,
            'statusMessage' => 'System SMS status is OFF.'
        ];

        // Checking of company SMS status

        $companySmsServiceStatus = $this->companyRepository->isCompanySmsServiceActive($company);

        if (!$companySmsServiceStatus) return [
            'status' => false,
            'statusMessage' => 'Company SMS status is OFF.'
        ];

        $smsSetting = $company->smsSetting;

        if (!$smsSetting->birthday_status) return [
            'status' => false,
            'statusMessage' => 'Birthday SMS status is OFF.'
        ];

        $beneficiaries = $this->companyRepository->beneficiariesByBirthDateList($birthDate, $company);

        $arrSentBeneficiaries = [];
        $arrFailedBeneficiaries = [];

        foreach ($beneficiaries as $beneficiary):

            $fullName = $beneficiary->full_name;
            $mobileNumber = $beneficiary->mobile_number;
            $dateOfBirth = $beneficiary->date_of_birth;

            $message = $this->birthdayTemplate([
                'full_name' => $fullName,
            ], $smsSetting->report_template);

            if (mobileNumberValidator($mobileNumber)):

                self::storeAndSendTransaction([
                    'transaction_date' => now()->format('Y-m-d'),
                    'transaction_type' => 1,
                    'sms_type' => 2,
                    'mobile_number' => $mobileNumber,
                    'message' => $message,
                    'source' => 'DAILY BIRTHDAY GREETING',
                    'scheduled_date' => null,
                    'scheduled_time' => null,
                ], $company);

                $arrSentBeneficiaries[] = [
                    'full_name' => $fullName,
                    'date_of_birth' => $dateOfBirth,
                    'mobile_number' => $mobileNumber,
                ];
            else:
                $arrFailedBeneficiaries[] = [
                    'full_name' => $fullName,
                    'date_of_birth' => $dateOfBirth,
                    'mobile_number' => $mobileNumber,
                ];
            endif;
            
        endforeach;

        return [
            'status' => true,
            'statusMessage' => 'Success.',
            'birthdate' => $birthDate,
            'sent_beneficiaries' => count($arrSentBeneficiaries)
                ? json_encode($arrSentBeneficiaries)
                : null,
            'failed_beneficiaries' => count($arrFailedBeneficiaries)
                ? json_encode($arrFailedBeneficiaries)
                : null,
        ];
    }

    public function generateCode($date, $company)
    {
        $smsTransaction = $company->smsTransactions()->whereYear('transaction_date', $date)->latest()->first();

        $smsTransactionCode = 'SMSTRNSCT-';
        $smsTransactionCode .= customDateFormat($date, 'MMYYYY') . leadingZeros($company->id) . '-';
        $smsTransactionCode .= self::formSmsTransactionCount($smsTransaction);

        return $smsTransactionCode;
    }

    public function formSmsTransactionCount($smsTransaction)
    {
        if (!$smsTransaction) return leadingZeros(1, $strType = 's', $padChar = 0, $padLength = 3);

        $smsTransactionCode = $smsTransaction->code;

        $arrSmsTransactionCode = explode('-', $smsTransactionCode);

        $currentSmsTransactionCode = (int) $arrSmsTransactionCode[2];

        return leadingZeros($currentSmsTransactionCode+1, $strType = 's', $padChar = 0, $padLength = 3);
    }

    public function computeTransactionAmount($smsTransaction)
    {
        $recipientsCount = $smsTransaction->smsRecipients()->where('status', 2)->count();

        $totalCreditAmount = $recipientsCount * $smsTransaction->credit_per_sent;

        $smsTransaction->update([
            'amount' => $totalCreditAmount,
            'updated_by' => 1
        ]);
    }

    public function computeCreditPerSent($smsTransaction)
    {
        $company = $smsTransaction->company;
        $smsSetting = $company->smsSetting;

        $chargePerSms = $smsTransaction->sms_type == 1
            ? ($smsSetting->credit_per_regular_sms ?: .30)
            : ($smsSetting->credit_per_branding_sms ?: .50);

        $creditPerSent = computeMessageCreditCharge(
            $smsTransaction->message,
            $chargePerSms,
            $smsSetting->max_char_per_sms
        );

        $smsTransaction->update([
            'credit_per_sent' => $creditPerSent,
            'updated_by' => Auth::id() ?: 1
        ]);
    }

    public function approveTransactionStatus($smsTransaction)
    {
        $smsTransaction->update([
            'status' => 2,
            'updated_by' => 1
        ]);
    }

    public function isRecipientRelated($smsTransaction, $recipientId)
    {
        $checking = $smsTransaction->smsRecipients()->where('id', $recipientId)->first();

        if (!$checking) return abort(404, 'SMS recipient is not related to SMS credit.');

        return $checking;
    }
}
?>