<?php

namespace App\Http\Repositories\Call;

use Illuminate\Support\Facades\Auth;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use Twilio\TwiML\VoiceResponse;

use App\Models\Slug;
use App\Models\Company;
use App\Models\CompanyCallTransaction;

use App\Http\Repositories\Base\SlugRepository;
use App\Http\Repositories\Base\CompanyRepository;
use App\Http\Repositories\Base\SystemSettingRepository;
use App\Http\Repositories\Base\CompanyCallTransactionRepository;

use App\Traits\MeSender;

class CallRepository
{
    use MeSender;

    public function __construct()
    {
        $this->slugRepository = new SlugRepository;
        $this->companyRepository = new CompanyRepository;
        $this->systemSettingRepository = new SystemSettingRepository;
        $this->companyCallTransactionRepository = new CompanyCallTransactionRepository;
    }

    public function voiceCall($request)
    {
        $model = Slug::findCodeOrDie($request->input('CompanyCode'));

        $company = $model->slug;

        $setting = $company->callSetting;

        $identity = $company->name;

        $voiceResponse = new VoiceResponse;

        $callerNumber = formatPhoneNumber($request->input('To'));

        $dial = $voiceResponse->dial(
            $callerNumber,
            [
                'callerId' => $setting->phone_no,
                'record' => $setting->is_recording,
                'recordingStatusCallback' => $setting->recording_status_url,
                'recordingStatusCallbackEvent' => 'in-progress completed absent',
            ]);

        $log = new Logger('twilio_record_logs');
        $log->pushHandler(new StreamHandler(storage_path('logs/twilio_record_logs.log')), Logger::INFO);
        $log->info('twilio_record_logs', [
            'action' => 'call', 
            'request' => $request->all()
        ]);

        return $voiceResponse;
    }

    public function statusCallback($request)
    {
        $callSid = $request->get('CallSid');
        $callDuration = $request->get('CallDuration');
        $callStatus = $request->get('CallStatus');

        $transaction = CompanyCallTransaction::where('call_sid', $callSid);

        if ($transaction):

            $transaction->update([
                'call_duration' => $callDuration,
                'call_status' => $callStatus,
                'updated_by' => 1,
            ]);
            
        endif;


        $log = new Logger('twilio_record_logs');
        $log->pushHandler(new StreamHandler(storage_path('logs/twilio_record_logs.log')), Logger::INFO);
        $log->info('twilio_record_logs', [
            'action' => 'statusCallback', 
            'request' => $request->all(),
            'callSid' => $request->get('CallSid'),
        ]);
    }

    public function fallback($request)
    {
        $log = new Logger('twilio_record_logs');
        $log->pushHandler(new StreamHandler(storage_path('logs/twilio_record_logs.log')), Logger::INFO);
        $log->info('twilio_record_logs', [
            'action' => 'fallback', 
            'request' => $request->all()
        ]);
    }

    public function recordingStatusCallback($request)
    {
        $callSid = $request->get('CallSid');
        $recordingSid = $request->get('RecordingSid');
        $recordingStatus = $request->get('RecordingStatus');
        $recordingDuration = $request->get('RecordingDuration');
        $recordingUrl = $request->get('RecordingUrl');

        if ($recordingStatus == 'completed'):
            $transaction = CompanyCallTransaction::where('call_sid', $callSid);

            if ($transaction):

                $transaction->update([
                    'recording_sid' => $recordingSid,
                    'recording_status' => $recordingStatus,
                    'recording_duration' => $recordingDuration,
                    'recording_url' => $recordingUrl,
                    'updated_by' => 1,
                ]);
                
            endif;
        endif;
        

        $log = new Logger('twilio_record_logs');
        $log->pushHandler(new StreamHandler(storage_path('logs/twilio_record_logs.log')), Logger::INFO);
        $log->info('twilio_record_logs', [
            'action' => 'recordingStatusCallback', 
            'request' => $request->all()
        ]);
    }


}
?>