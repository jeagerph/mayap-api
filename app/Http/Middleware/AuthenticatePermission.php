<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Module;
use App\Models\AccountPermission;

class AuthenticatePermission
{
    public $message = [
        'access' => 'You do not have permission to access this page.',
        'index' => 'You do not have permission to load a resource.',
        'store' => 'You do not have permission to create a resource.',
        'update' => 'You do not have permission to update a resource.',
        'destroy' => 'You do not have permission to delete a resource.'
    ];

    public function handle($request, Closure $next)
	{
		if(!self::isAdministrator($request)) self::gate($request);

		return $next($request);
    }
    
    protected function isAdministrator($request)
	{
		return Auth::user()->account->account_type === 1;
    }
    
    protected function gate($request)
	{
        $user = Auth::user();

        $account = $user->account;

        $accountType = $account->account_type;
        
		// Get accessing route's name: e.g. roles.index
		$routeName = Route::getCurrentRoute()->getName();

		// throws 400 Bad Request ErrorException
		if(is_null($routeName)) return abort(400, 'Route name not found');

		$routeName = explode('.', $routeName);
		
		// Get module of accessing route's name: e.g. roles
		$module = Module::where('route_name', $routeName[0])->first();
		
		if(is_null($module)) return abort(403, 'You do not have permission to perform this action.');
		
		$action = strtolower($routeName[1]);
		
		$permission = AccountPermission::where('account_id', $account->id)->where('module_id', $module->id)->where($action, 1)->first();

		if(!$permission) return abort(403, $this->message[$action]);

		return true;
	}
}
