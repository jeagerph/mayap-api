<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Repositories\Call\CallRepository as Repository;

class CallController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth.permission');

        $this->repository = new Repository;
    }

    public function voiceCall(Request $request)
    {
        $request->merge([
            'call-related' => true
        ]);

        return $this->repository->voiceCall($request);
    }

    public function statusCallback(Request $request)
    {
        $request->merge([
            'call-related' => true
        ]);

        return $this->repository->statusCallback($request);
    }

    public function fallback(Request $request)
    {
        $request->merge([
            'call-related' => true
        ]);

        return $this->repository->fallback($request);
    }

    public function recordingStatusCallback(Request $request)
    {
        $request->merge([
            'call-related' => true
        ]);

        return $this->repository->recordingStatusCallback($request);
    }
}
