<?php

use Illuminate\Support\Str;

if(!function_exists('randomString'))
{
    function randomString($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

if(!function_exists('randomNumbers'))
{
    function randomNumbers($length = 10) 
    {
		$result = '';
	
		for($i = 1; $i <= $length; $i++) {
			$result .= mt_rand(1, 9);
		}
	
		return $result;
    }
}

if(!function_exists('customDateFormat'))
{
    function customDateFormat($date, $format = 'MMDDYY')
    {
        $exploded = explode('-', $date);

        $year = $exploded[0];

        $month = $exploded[1];

        $day = $exploded[2];
        
        $formattedDate = $date;

        switch($format):

            case 'MMDDYY':

                $formattedDate = $month . $day . $year[2] . $year[3];
            break;

            case 'MMYYYY':

                $formattedDate = $month . $year;

            break;

            default:
                $year = $exploded[0];

                $month = $exploded[1];

                $day = $exploded[2];

                $formattedDate = $month . $day . $year[2] . $year[3];
            break;

        endswitch;

        return $formattedDate;
    }
}

if(!function_exists('generateFilename'))
{
    function generateFilename($file, $folder)
    {
        $name = Str::random(16);
        $name .= $name . time();
        $name .= $file->getClientOriginalExtension();

        $path = $folder . '/';
        $path .= $path . now()->format('Y') . '/';
        $path .= $path . now()->format('F') . '/';
        $path .= $path . now()->format('d') . '/';
        $path .= $path . $name;

        return [
            'path' => $path,
            'name' => $name
        ];
    }
}

if(!function_exists('randomFileName'))
{
    function randomFileName($extensionName = '.jpg')
    {
        $fileName = Str::random(16);
        $fileName .= $fileName . time();
        $fileName .= $extensionName;

        return $fileName;
    }
}

if(!function_exists('randomFilePath'))
{
    function randomFilePath($folder, $fileName)
    {
        $filePath = $folder . '/';
        $filePath .= now()->format('Y') . '/';
        $filePath .= now()->format('F') . '/';
        $filePath .= now()->format('d') . '/';
        $filePath .= $fileName;

        return $filePath;
    }
}

if(!function_exists('leadingZeros'))
{
    function leadingZeros($string, $strType = 's', $padChar = 0, $padLength = 4)
    {
        $format = "%{$padChar}{$padLength}{$strType}";

        return sprintf($format, $string);
    }
}

if(!function_exists('formPath'))
{
    function formPath($folderName, $name) 
    {
        $path = $folderName;
        $path .= now()->format('Y') . '/';
        $path .= now()->format('F') . '/';
        $path .= now()->format('d') . '/';
        $path .= $name;

        return $path;
    }
}

if(!function_exists('formDocumentTable'))
{
    function formDocumentTable($table) 
    {
        $fields = $table->fields;
        $values = $table->values;

        $html = "<div class='ap-content-vehicle'>";
        $html .= "<table class='ap-vehicle-table'>";
        $html .= "<thead>";
        $html .= "<tr>";

        foreach($fields as $key => $field):
            $html .= "<th><small>{$field->label}</small></th>";
        endforeach;

        $html .= "</tr>";
        $html .= "</thead>";
        $html .= "<tbody>";

        foreach($values as $value):

            $html .= "<tr>";

            foreach($fields as $key => $field):

                $html .= "<td><small>{$value->{$field->key}}</small></td>";

                if ($key == count($fields)-1) $html .= "</tr>";

            endforeach;

        endforeach;

        $html .= "</tbody></table></div>";

        return $html;
    }
}

if(!function_exists('formatMobileNumber'))
{
    function formatMobileNumber($mobile)
    {
        $trim = ltrim($mobile, '0');

        return '+63' . $trim;
    }
}

if(!function_exists('readableFileSize'))
{
    function readableFileSize($bytes, $dec = 2) {
        $size   = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$dec}f %s", $bytes / (1024 ** $factor), $size[$factor]);
      }
}

if(!function_exists('computeMessageCreditCharge'))
{
    function computeMessageCreditCharge($message, $chargePerSms, $maxChar = 159)
    {
        if (strlen($message) <= $maxChar):
            $smsCount = 1;
        else:
            if ($maxChar <= 0) $maxChar = 159;
            
            $smsCount = round(strlen($message) / $maxChar);
        endif;

        return $smsCount *  $chargePerSms;
    }
}

if(!function_exists('computeNoOfCredit'))
{
    function computeNoOfCredit($message, $maxChar = 159)
    {
        if (strlen($message) <= $maxChar):
            $smsCount = 1;
        else:
            $smsCount = round(strlen($message) / $maxChar);
        endif;

        return $smsCount;
    }
}

if(!function_exists('computeCallMinutes'))
{
    function computeCallMinutes($minutes)
    {
        // Check minutes value first

        return $minutes;
    }
}

if(!function_exists('mobileNumberValidator'))
{
    function mobileNumberValidator($mobileNumber)
    {
        if (!$mobileNumber || $mobileNumber[0] != '0' || $mobileNumber[1] != '9' || strlen($mobileNumber) != 11)
            return false;

        return true;
    }
}

if(!function_exists('formSmsMessage'))
{
    function formSmsMessage($message, $headerName, $footerName, $insertFooter)
    {
        $newMessage = $headerName
            ? $headerName . PHP_EOL
            : '';
        
        $newMessage .= $message;

        if($insertFooter):
            $newMessage .= PHP_EOL . PHP_EOL;
            $newMessage .= $footerName;
        endif;

        return $newMessage;
    }
}

if(!function_exists('diafaanMobileNumber'))
{
    function diafaanMobileNumber($mobile)
    {
        $trim = ltrim($mobile, '0');

        return '+63' . $trim;
    }
}

if(!function_exists('phoneNumberValidator'))
{
    function phoneNumberValidator($mobileNumber)
    {
        // if (!$mobileNumber || $mobileNumber[0] != '0' || $mobileNumber[1] != '9' || strlen($mobileNumber) != 11)
        //     return false;

        return true;
    }
}

if(!function_exists('formatPhoneNumber'))
{
    function formatPhoneNumber($mobile)
    {
        $trim = ltrim($mobile, '0');

        return '+63' . $trim;
    }
}

if(!function_exists('forceHttps'))
{
    function forceHttps($currentUrl)
    {
        $url = $currentUrl;

        if(!str_contains($url, 'https')):
            if (env('APP_USE_HTTPS')):
                $url = str_replace('http', 'https', $url);
            endif;
        endif;

        return $url;
    }
}

if(!function_exists('listDatesFromDateRange'))
{
    function listDatesFromDateRange($startDate, $endDate, $format = 'Y-m-d') 
    {
        $arrDates = [];

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date):
            $arrDates[] = $date->format($format);
        endforeach;

        return $arrDates;
    }
}
?>