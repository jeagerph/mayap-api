<?php

namespace App\Http\Controllers\API\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Repositories\MyCompany\CallRepository as Repository;

use App\Http\Requests\MyCompany\Call\CallRequest;

class CallController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function showSetting(Request $request)
    {
        $request->merge([
            'my-company-call-setting-related' => true
        ]);

        return $this->repository->showSetting($request);
    }

    public function generateToken(Request $request)
    {
        $request->merge([
            'my-company-call-setting-related' => true
        ]);

        return $this->repository->generateToken($request);
    }

    public function storeCallTransaction(Request $request)
    {
        $request->merge([
            'my-company-call-transactions-related' => true
        ]);

        return $this->repository->storeCallTransaction($request);
    }

    public function updateCallTransaction(Request $request, $code)
    {
        $request->merge([
            'my-company-call-transactions-related' => true
        ]);

        return $this->repository->updateCallTransaction($request, $code);
    }

    public function showCallTransactionRecording(Request $request, $code)
    {
        $request->merge([
            'my-company-call-transactions-related' => true
        ]);

        return $this->repository->showCallTransactionRecording($request, $code);
    }
}
