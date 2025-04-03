<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait MessageTemplate
{
    public function reportTemplate($data, $template = 'default')
    {
        if ($template == 'mayap'):

            $message = 'MAYAP UPDATE AS OF ' . $data['report_date'] . ' ' . $data['report_time'] . ':' . PHP_EOL . PHP_EOL;

            $message .= 'BENEFICIARIES' . PHP_EOL;
            $message .= '- TOTAL: ' . $data['beneficiaries']['total'] . PHP_EOL;
            $message .= '- NEW: ' . $data['beneficiaries']['date'] . PHP_EOL . PHP_EOL;

            $message .= 'OFFICERS/LEADERS' . PHP_EOL;
            $message .= '- TOTAL: ' . $data['officers']['total'] . PHP_EOL;
            $message .= '- NEW: ' . $data['officers']['date'] . PHP_EOL . PHP_EOL;

            $message .= 'REFERRAL/NETWORK' . PHP_EOL;
            $message .= '- TOTAL: ' . $data['networks']['total'] . PHP_EOL;
            $message .= '- NEW: ' . $data['networks']['date'] . PHP_EOL;
            $message .= '- INCENTIVES: ' . $data['incentives']['date'] . PHP_EOL . PHP_EOL;

            $message .= 'HOUSEHOLD' . PHP_EOL;
            $message .= '- TOTAL: ' . $data['household']['total'] . PHP_EOL;
            $message .= '- NEW: ' . $data['household']['date'] . PHP_EOL;
            $message .= '- BARANGAY COVERED: ' . count($data['householdByBarangay']) . PHP_EOL;
            $message .= '- SITIO/PUROK COVERED: ' . count($data['householdByPurok']) . PHP_EOL . PHP_EOL;

            $message .= 'ASSISTANCE' . PHP_EOL;
            $message .= '- REQUESTED: ' . $data['requested']['date'] . PHP_EOL;
            $message .= '- ASSISTED: ' . $data['assisted']['date'] . PHP_EOL . PHP_EOL;

            foreach ($data['assistancesByType'] as $assistance):
                $message .= '-- ' . $assistance->name . ' (' . ($assistance->total ?: '0') . ')' . PHP_EOL;
            endforeach;
        
        else:
            $message = 'SYSTEM UPDATE AS OF ' . $data['report_date'] . ' ' . $data['report_time'] . ':' . PHP_EOL . PHP_EOL;
            $message .= 'TOTAL BENEFICIARIES: ' . $data['beneficiaries']['total'] . PHP_EOL;
            $message .= 'New Beneficiaries: ' . $data['beneficiaries']['date'] . PHP_EOL;
            $message .= 'New Officers: ' . $data['officers']['date'] . PHP_EOL;
            $message .= 'New Household: ' . $data['household']['date'] . PHP_EOL;
            $message .= 'New Networks: ' . $data['networks']['date'] . PHP_EOL;
            $message .= 'New Incentive: ' . $data['incentives']['date'] . PHP_EOL;
            $message .= 'New Requested Assistance: ' . $data['requested']['date'] . PHP_EOL;
            $message .= 'New Assisted Assistance: ' . $data['assisted']['date'] . PHP_EOL;
        endif;
        

        return $message;
    }

    public function birthdayTemplate($data, $template = 'default')
    {
        if ($template == 'mayap'):
            $message = 'Hi ' . $data['full_name'] . '!' . PHP_EOL;
            $message .= 'Wishing you a Happy Birthday filled with success, good health and prosperity.' . PHP_EOL . PHP_EOL;
            $message .= 'From team maYap';
        else:
            $message = 'Hi ' . $data['full_name'] . '!' . PHP_EOL;
            $message .= 'Wishing you a Happy Birthday filled with success, good health and prosperity.' . PHP_EOL . PHP_EOL;
        endif;

        return $message;
    }
}

?>