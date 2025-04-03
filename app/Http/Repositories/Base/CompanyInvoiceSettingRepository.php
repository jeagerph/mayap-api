<?php

namespace App\Http\Repositories\Base;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\CompanyInvoiceSetting;

class CompanyInvoiceSettingRepository
{
    public $defaultData = [
        'domain_hosting' => 1040.00,
        'branding_sms' => 0.50,
        'regular_sms' => 0.30,
        'virtual_storage' => 1000.00,
        'show_left_representative' => 1,
        'left_representative_name' => 'MR./MS.',
        'left_representative_position' => 'COMPANY REPRESENTATIVE',
        'show_right_representative' => 0,
        'right_representative_name' => 'MR./MS.',
        'right_representative_position' => 'COMPANY REPRESENTATIVE',
    ];

    public function new($data)
    {
        return new CompanyInvoiceSetting([
            'domain_hosting' => $data['domain_hosting'],
            'branding_sms' => $data['branding_sms'],
            'regular_sms' => $data['regular_sms'],
            'virtual_storage' => $data['virtual_storage'],
            'show_left_representative' => $data['show_left_representative'],
            'left_representative_name' => $data['left_representative_name'],
            'left_representative_position' => $data['left_representative_position'],
            'show_right_representative' => $data['show_right_representative'],
            'right_representative_name' => $data['right_representative_name'],
            'right_representative_position' => $data['right_representative_position'],
            'created_by' => Auth::id() ?: 1
        ]);
    }
}
?>