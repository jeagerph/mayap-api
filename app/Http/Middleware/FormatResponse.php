<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;

class FormatResponse
{
    public function handle($request, Closure $next)
	{
		$response 	= $next($request);

		$isOauth = Str::contains($request->path(), 'oauth');

		$content = $isOauth ? $response->getContent() : $response->getOriginalContent();
		
		return response($content, $response->getStatusCode());
	}
}
