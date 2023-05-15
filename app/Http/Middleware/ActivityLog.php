<?php

namespace App\Http\Middleware;
use App\UserActivityLog;

use Closure;

class ActivityLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // return $next($request);

        $response = $next($request);

        if(auth()->user()) {
            if(auth()->id() != 1928){
                if(strpos(request()->path(), 'fetch') === false){
                    UserActivityLog::create([
                        'user_agent' => request()->userAgent(),
                        'method' => request()->method(),
                        'url' => request()->fullUrl(),
                        'ip' => request()->ip(),
                        'remark' => request()->path(),
                        'created_by' => auth()->id(),
                    ]);
                    if(preg_match('/MSIE/i', request()->userAgent()) && !preg_match('/Opera/i', request()->userAgent())){
                        return view('404')->with('message', 'Please use browser other than "INTERNET EXPLORER."'); 
                    }
                }      
            }
        }

        return $response;
    }
}
