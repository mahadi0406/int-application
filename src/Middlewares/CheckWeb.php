<?php

namespace Debugsolver\Bappe\Middlewares;
use Closure;

class CheckWeb
{
    public function handle($request, Closure $next)
    {
        if(!$this->alreadyInstalled()){
            return $next($request);
        }
        abort(404);
    }

    public function alreadyInstalled()
    {
        $exists = file_exists(storage_path('logins'));
        if($exists){
        	$fileData = file_get_contents(storage_path('logins'));
        	if(base64_decode($fileData) == config('xsenderinfo.unique_id')){
        		return true;
        	}else{
        		return false;
        	}
        }else{
        	return false;
        }
    }
}
