<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'namespace' => 'API',
    'middleware' => ['format.response']
], function()
{
    Route::post('login', 'AuthController@login');

    Route::post('login/otp', 'AuthController@loginWithOtp');

    Route::post('validate/otp', 'AuthController@validateOtp');

    Route::post('mobile/login', 'AuthController@mobileLogin');

    Route::get('test', 'AuthController@test');

    Route::post('voice/call', 'CallController@voiceCall');

    Route::post('voice/call/status/callback', 'CallController@statusCallback');

    Route::post('voice/call/fallback', 'CallController@fallback');

    Route::post('voice/call/recording-status-callback', 'CallController@recordingStatusCallback');

    Route::get('document/validate/{code}', 'PublicController@viewDocument');

    Route::get('identification/validate/{code}', 'PublicController@viewIdentification');

    // Route::post('contact', 'PublicController@contact');

    Route::post('password/forgot', 'PublicController@forgotPassword');

    Route::post('password/reset', 'PublicController@resetPassword');

});

Route::group([
    'namespace' => 'API',
    'middleware' => [
        'auth:api',
        'format.response'
    ]
], function()
{
    Route::get('logout', 'AuthController@logout');

    include('api/my-account.php');
});


Route::group([
    'namespace' => 'API\Administration',
    'prefix' => 'administration',
    'middleware' => [
        'auth:api',
        'format.response'
    ]
], function()
{
    include('api/administration/accounts.php');

    include('api/administration/companies.php');
});

Route::group([
    'namespace' => 'API\MyCompany',
    'prefix' => 'my-company',
    'middleware' => [
        'auth:api',
        'format.response'
    ]
], function()
{
    include('api/my-company/dashboard.php');

    include('api/my-company/sms.php');

    include('api/my-company/accounts.php');

    include('api/my-company/classifications.php');

    include('api/my-company/beneficiaries.php');

    include('api/my-company/calls.php');

    include('api/my-company/monitoring.php');

    include('api/my-company/settings.php');

    include('api/my-company/questionnaires.php');

    include('api/my-company/assignatories.php');

    include('api/my-company/id-templates.php');

    include('api/my-company/document-templates.php');

    include('api/my-company/patients.php');

    include('api/my-company/assistances.php');
    
    include('api/my-company/incentives.php');

    include('api/my-company/officer-classifications.php');
    
    include('api/my-company/voters.php');
});



// Route::fallback(function()
// {
//     return abort('404', 'Sorry, your request is not found.');
// });
