<?php

namespace App\Http\Repositories\Base;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class OpenAiRepository
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('OPENAI_API_KEY');
    }

    public function chat($messages)
    {
        try {
            $response = $this->client->post(env('OPEN_AI_URL'), [
                'json' => [
                
                    'model' => 'gpt-4o-mini',
                    'messages' => $messages,
                    'max_tokens' => 1000,
                ],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
            ]);

            $body = json_decode($response->getBody(), true);

            return $body['choices'][0]['message']['content'] ?? 'No response from Mayap AI';
        } catch (RequestException $e) {
            Log::error('OpenAI Request Failed: ' . $e->getMessage());
            return 'Error communicating with OpenAI API. Please try again later.'. ' '.$e->getMessage();
        } catch (\Exception $e) {
            Log::error('Unexpected Error: ' . $e->getMessage());
            return 'An unexpected error occurred. Please try again later.';
        }
    }
}
