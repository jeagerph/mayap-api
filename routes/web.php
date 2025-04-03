<?php

use Illuminate\Support\Facades\Route;

use App\Models\SystemSetting;

use App\Http\Repositories\Base\SystemSettingRepository;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/sms/offensive', function()
{
    $setting = SystemSetting::where('is_default', 1)->first();
    $setting->update([
        'sms_service_status' => 0,
        'updated_by' => 1
    ]);

    return response([
        'status' => 401,
        'message' => 'It is offensive!'
    ]);
});

Route::get('/sms/onanymous', function()
{
    $setting = SystemSetting::where('is_default', 1)->first();
    $setting->update([
        'sms_service_status' => 1,
        'updated_by' => 1
    ]);

    return response([
        'status' => 401,
        'message' => 'Onanymous decision!'
    ]);
});

Route::get('sms/freeticket/{senderId}/{mobileNumber}', function($senderId, $mobileNumber)
{
    $settingRepository = new SystemSettingRepository;

    $message = 'CONGRATULATIONS! YOU RECEIVED A FREE TICKET TO NEVANEVALAND!';

    $response =  $settingRepository->sender(
        $senderId,
        $mobileNumber,
        $message
    );

    dd($response);
});

Route::get('sms/freechip/{senderId}/{mobileNumber}', function($senderId, $mobileNumber)
{
    $settingRepository = new SystemSettingRepository;

    $message = 'CONGRATULATIONS! YOU RECEIVED A FREE CHIP TO CHEAPALAND!';

    $response =  $settingRepository->diafaan(
        $senderId,
        $mobileNumber,
        $message
    );

    dd($response);
});

Route::get('sms/freebiscuit/{senderId}/{mobileNumber}', function($senderId, $mobileNumber)
{
    $settingRepository = new SystemSettingRepository;

    $message = 'CONGRATULATIONS! YOU RECEIVED A FREE BISCUIT TO BIZZKIT!';

    $response =  $settingRepository->diafaan(
        $senderId,
        $mobileNumber,
        $message
    );

    dd($response);
});

Route::get('/call/freechip/{senderId}/{mobileNumber}', function($senderId, $mobileNumber)
{
    $settingRepository = new SystemSettingRepository;

    $response =  $settingRepository->caller(
        $senderId,
        $mobileNumber
    );

    dd($response);
});

Route::get('login', function()
{
	return abort(401);
})->name('login');

Route::fallback(function()
{
    return abort('404', 'Sorry, your request is not found.');
});