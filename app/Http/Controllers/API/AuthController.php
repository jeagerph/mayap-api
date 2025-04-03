<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Models\User;
use App\Models\UserPin;

use App\Http\Repositories\Base\PassportRepository;
use App\Http\Repositories\Base\UserPinRepository;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ValidateOtpRequest;

class AuthController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->passportRepository = new PassportRepository;
        $this->pinRepository = new UserPinRepository;
    }

    public function login(Request $request, LoginRequest $formRequest)
	{
        if($this->attemptLogin($formRequest)):

            $user = User::where('username', $formRequest->input('username'))->whereNull('deleted_at')->latest()->first();

            return $this->passportRepository->loginHttpClient(
                $formRequest->input('username'),
                $formRequest->input('password'),
                $request
            );

		endif;

		return $this->sendFailedLoginResponse($formRequest);
    }

    public function loginWithOtp(Request $request, LoginRequest $formRequest)
	{
        if($this->attemptLogin($formRequest)):

            $user = User::where('username', $formRequest->input('username'))->whereNull('deleted_at')->latest()->first();

            if ($user->account->account_type == 2):

                $company = $user->company();

                if ($company->smsSetting->otp_status):
                    if (!mobileNumberValidator($user->account->mobile_number)):
                        return response([
                            'errors' => [
                                'mobile_number' => ['Account has no valid mobile number. Kindly contact your System Provider to assist you.']
                            ] 
                        ], 422);
                    endif;
        
                    $userPin = $this->pinRepository->generate($user);
        
                    $this->pinRepository->sendOtp($user);
        
                    return response([
                        'message' => 'OTP has been sent to registered mobile number.',
                        'token' => $userPin->token,
                    ], 200);
                endif;
            endif;

            return $this->passportRepository->login(
                $formRequest->input('username'),
                $formRequest->input('password'),
                $request
            );
		endif;

		return $this->sendFailedLoginResponse($formRequest);
    }

    public function validateOtp(Request $request, ValidateOtpRequest $formRequest)
    {
        $userPin = UserPin::where('token',  $formRequest->input('token'))->first();

        $user = $userPin->user;

        if ($userPin->otp == $formRequest->input('otp')):
            
            $user->pins()->delete();

            return $this->passportRepository->login(
                $formRequest->input('username'),
                $formRequest->input('password'),
                $request
            );
        endif;

        return response([
            'errors' => [
                'otp' => ['Invalid OTP.']
            ]
        ], 422);
    }
    
    protected function attemptLogin($request)
	{
		$user = User::where('username', $request->input('username'))->whereNull('deleted_at')->latest()->first();

		return $user && !$user->locked && $this->passportRepository->validatePassword($user, $request->input('password'));
    }
    
    protected function sendFailedLoginResponse($request)
	{
		$attemptingUser = User::where('username', $request->input('username'))->first();

		if(!is_null($attemptingUser)):
			if($this->passportRepository->validatePassword($attemptingUser, $request->input('password'))):

				if($attemptingUser->locked)
					return abort(401, 'Account is locked.');

			endif;
		endif;

		return abort(401, 'These credentials do not match our records.');
	}
    
    public function logout(Request $request)
	{
		$request->user()->token()->revoke();

		return response(['message' => 'Successfully logged out'], 200);
    }
    
    public function test(Request $request)
    {
        return User::all();
    }
}
