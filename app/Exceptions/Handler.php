<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public $messages = [
        '400' => 'Bad Request', 
        '401' => 'Unauthorized', 
        '403' => 'Forbidden', 
        '404' => 'Not Found', 
        '405' => 'Method Not Allowed', 
        '410' => 'Gone', 
        '415' => 'Unsupported Media Type', 
        '422' => 'Unprocessable Entity', 
        '429' => 'Too Many Requests', 
        '500' => 'Internal Server Error', 
        '503' => 'Service unavailable'
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    public function render($request, Throwable $exception)
    {
        $class = get_class($exception);

        if($class == 'League\OAuth2\Server\Exception\OAuthServerException'):

            $code = $exception->getHttpStatusCode();
            $message = $exception->getMessage();
            
            return response([
                'error' => [
                    'oauth' => true,
                    'code' => $code,
                    'message' => self::statusMessage($code),
                    'description' => $message
                ]
            ], $code);

        elseif($class == 'Illuminate\Auth\AuthenticationException'):

            $code = 401;
            $message = $exception->getMessage();

            return response([
                'error' => [
                    'oauth' => true,
                    'code' => $code,
                    'message' => self::statusMessage($code),
                    'description' => $message
                ]
            ], $code);

        elseif($this->isHttpException($exception)):

            $code = $exception->getStatusCode();
            $message = $exception->getMessage();
            
            return response([
                'error' => [
                    'oauth' => false,
                    'code' => $code,
                    'message' => self::statusMessage($code),
                    'description' => $message
                ]
            ], $code);
		endif;
		
        return parent::render($request, $exception);
	}
	
	private function statusMessage($code)
	{
		if(array_key_exists($code, $this->messages))
			return $this->messages[$code];

		return 'Request Error';
    }
}
