<?php
namespace App\Traits;

use App\Mail\MailBuilder;
use Illuminate\Support\Facades\Mail;
use App\Notifications\DatabaseBroadcast;
use Illuminate\Support\Facades\Notification;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use App\Models\SystemSetting;

trait MeSender
{
    public function fireMail($to, $view, $subject, $model)
    {
        $data = [
            'view' => $view,
            'subject' => $subject,
            'model' => $model
        ];
        
        Mail::to($to)->send(new MailBuilder($data));

        return count(Mail::failures()) > 0;
    }

    public function fireBrandingSmsToRecipient($recipient, $apiUrl, $apiKey)
    {
        $mobileNumber = $recipient->mobile_number;
        $message = $recipient->message;

        if(env('APP_ENV') === 'production'):

            $response = $this->fireTxtbox(
                $apiUrl ?: env('BARANGAY_TXTBOX_URL'),
                $apiKey ?: env('BARANGAY_TXTBOX_API_KEY'),
                $mobileNumber,
                $message
            );

        else:

            $response = [
                'statusCode' => 200,
                'response' => 'DEVELOPMENT',
            ];
        
        endif;

        return [
            'statusCode' => $response['statusCode'],
            'response' => $response['response']
        ];
    }

    public function fireRegularSmsToRecipient($recipient)
    {
        $mobileNumber = $recipient->mobile_number;
        $message = $recipient->message;

        if(env('APP_ENV') === 'production'):

            $response = $this->fireDiafaanDefault(
                diafaanMobileNumber($mobileNumber),
                $message
            );

        else:

            $response = [
                'statusCode' => 200,
                'response' => 'DEVELOPMENT',
            ];
        
        endif;

        return [
            'statusCode' => $response['statusCode'],
            'response' => $response['response']
        ];
    }

    public function fireFreeMessage($mobileNumber, $message)
    {
        if(env('APP_ENV') === 'production'):

            $response = $this->fireTxtbox(
                env('VELCRO_TXTBOX_URL'),
                env('VELCRO_TXTBOX_API_KEY'),
                $mobileNumber,
                $message
            );

        else:

            $response = [
                'statusCode' => 200,
                'response' => 'DEVELOPMENT',
            ];
        
        endif;

        return $response;
    }

    public function fireTxtbox($apiUrl, $apiKey, $mobileNumber, $message)
    {
        $log = new Logger('txtbox_logs');

        $setting = SystemSetting::where('is_default', 1)->first();

        if (!$setting->sms_service_status):
            return [
                'response' => 'Maintenance mode.',
                'statusCode' => 001
            ];
        endif;

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => array(
                'message' => $message,
                'number' => $mobileNumber
            ),
            CURLOPT_HTTPHEADER => array(
                "X-TXTBOX-Auth: {$apiKey}"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $log->pushHandler(new StreamHandler(storage_path('logs/txtbox_logs.log')), Logger::INFO);

        $log->info('txtbox_logs', [
            'response' => $response,
            'err' => $err,
            'mobile_number' => $mobileNumber,
            'message' => $message,
            'api_url' => $apiUrl,
            'api_key' => $apiKey,
        ]);

        if ($err):
            return [
                'response' => $response,
                'statusCode' => $err
            ];
        endif;

        return [
            'response' => $response,
            'statusCode' => 200
        ];

    }

    public function fireDiafaanDefault($mobileNumber, $message)
    {
        $model = new \App\Models\Diafaan\DefaultSMS;

        $model->setConnection('diafaanDefaultMysql');

        $new = $model->insert([
            'MessageTo' => $mobileNumber,
            'MessageText' => $message
        ]);

        if($new):
            return [
                'statusCode' => 200,
                'response' => 'Message is successfully sent using diafaan.'
            ];
        else:
            return [
                'statusCode' => 200,
                'response' => 'Diafaan error.'
            ];
        endif;
    }

    public function fireDiafaanOtp($mobileNumber, $message)
    {
        $model = new \App\Models\Diafaan\OtpSMS;

        $model->setConnection('diafaanOtpMysql');

        $result = $model->insert([
            'MessageTo' => $mobileNumber,
            'MessageText' => $message,
        ]);

        $response = 'OTP successfully sent to diafaan.';

        return [
            'response' => $response,
            'statusCode' => 200
        ];
    }

    public function fireCall($accountSID, $authToken, $authUrl, $accountPhoneNo,  $destinationPhoneNo)
    {
        $client = new \Twilio\Rest\Client($accountSID, $authToken);

        // Initiate call and record call
        $call = $client->account->calls->create(
            $destinationPhoneNo, // Destination phone number
            $accountPhoneNo, // Valid Twilio phone number
            [
                "url" => $authUrl,
                "twiml" => "<Response><Say>Ahoy, World!</Say></Response>"
            ]
        );

        if($call):
            $result = 200;
        else:
            $result = 401;
        endif;

        $log = new Logger('twilio_logs');

        $log->pushHandler(new StreamHandler(storage_path('logs/twilio_logs.log')), Logger::INFO);

        $log->info('twilio_logs', [
            'account_SID' => $accountSID,
            'auth_token' => $authToken,
            'auth_url' => $authUrl,
            'account_phone_no' => $accountPhoneNo,
            'destination_phone_no' => $destinationPhoneNo,
            'call_response' => $call, 
            'result' => $result
        ]);

        return [
            'response' => $call,
            'statusCode' => $result
        ];
    }

    // public function fireNotification($users, $notifiable)
    // {
    //     Notification::send($users, new DatabaseBroadcast($notifiable));
    // }

    // public function fireBroadcast($model)
    // {
    //     broadcast($model);
    // }
}
?>
