<?php

namespace App\Http\Repositories\Base;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PassportRepository
{
    public function login($username, $password, $request)
    {
        $ticket = [
            'username' => $username, 
            'password' => $password,
            'grant_type' => 'password', 
            'client_id' => env('PASSPORT_CLIENT_ID', 2), 
            'client_secret' => env('PASSPORT_CLIENT_SECRET', '')
        ];

        // dd($ticket);

        Storage::append('test.txt', $username . '-' . $password . '-' . now()->format('Y-m-d H:iA'));

        $request->request->add($ticket);

        $proxy = Request::create(
            env('PASSPORT_AUTH_ENDPOINT', 'oauth/token'),
            'POST'
        );

        return Route::dispatch($proxy)->getContent();
    }

    public function loginHttpClient($username, $password, $request)
    {
        $http = new \GuzzleHttp\Client;
        
        $ticket = [
            'username' => $username, 
            'password' => $password,
            'grant_type' => 'password', 
            'client_id' => env('PASSPORT_CLIENT_ID', 2), 
            'client_secret' => env('PASSPORT_CLIENT_SECRET', ''),
            'scope' => '*'
        ];

        Storage::append('test.txt', $username . '-' . $password . now()->format('Y-m-d H:iA'));

        $response = $http->post(env('APP_URL').'/'.env('PASSPORT_AUTH_ENDPOINT'), [
            'form_params' => $ticket,
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    public function validatePassword($user, $password)
    {
        return Hash::check($password, $user->password);
    }
}
?>