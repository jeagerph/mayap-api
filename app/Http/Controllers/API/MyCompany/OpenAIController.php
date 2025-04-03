<?php

namespace App\Http\Controllers\Api\MyCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Repositories\Base\OpenAiRepository;

class OpenAIController extends Controller
{
    protected $openAiRepository;

    public function __construct(OpenAiRepository $openAiRepository)
    {
        $this->openAiRepository = $openAiRepository;
    }

    public function askOpenAI(Request $request)
    {
   
        $userQuery = $request->input('ask');

        
        $messages = [
            ['role' => 'system', 'content' => 'You are a helpful assistant.'],
            ['role' => 'user', 'content' => $userQuery],
        ];

       
        $response = $this->openAiRepository->chat($messages);

        return response()->json(['response' => $response]);
    }
}
