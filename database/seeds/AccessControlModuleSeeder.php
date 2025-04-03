<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessControlModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('modules')->insert([
            [
                'is_admin' => 1,
                'name' => 'Admin / Prerequisites',
                'route_name' => 'prerequisites',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'is_admin' => 1,
                'name' => 'Admin / Accounts',
                'route_name' => 'admin-accounts',
                'created_at' => now(),
                'created_by' => 1,
            ],
            [
                'is_admin' => 1,
                'name' => 'Admin / Companies',
                'route_name' => 'admin-companies',
                'created_at' => now(),
                'created_by' => 1,
            ],

            // COMPANY MODULES

            [
                'is_admin' => 0,
                'name' => 'Company / Accounts',
                'route_name' => 'company-accounts',
                'created_at' => now(),
                'created_by' => 1,
            ],

            [
                'is_admin' => 0,
                'name' => 'Company / Members',
                'route_name' => 'company-members',
                'created_at' => now(),
                'created_by' => 1,
            ],

        ]);

        DB::table('system_settings')->insert([
            [
                'sms_service_status' => 1,
                'branding_api_url' => 'https://ws-v2.txtbox.com/messaging/v1/sms/push',
                'branding_api_code' => '1a5fef9024bab8040ef8f22b98e7b2ea',
                'call_service_status' => 1,
                'call_account_sid' => 'ACd1d19a5cb3dcfd8878652af22844e780',
                'call_auth_token' => '8166a756ccd7856df01ce91940e2bbc5',
                'call_auth_url' => 'http://demo.twilio.com/docs/voice.xml',
                'call_phone_no' => '+13253133642',
                'is_default' => 1,
                'created_at' => now(),
                'created_by' => 1,
            ],
        ]);
    }
}
