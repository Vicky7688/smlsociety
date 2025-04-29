<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\SessionMaster;

class CheckSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
      if(session('sessionId')){
            $sesstion = SessionMaster::where('id', session('sessionId'))->first();
            if($sesstion){
                if($sesstion->auditPerformed == "Yes"){
                       if(!in_array($request->actiontype, ['getdata','getaccount','getInstallmets','getLoanAc','getloandetails'])){
                               return response()->json(['statuscode'=>'ERR', 'status'=>'Session Audit completed', 'message' => "Session Audit completed"],400);
                       }
                   
                }
            }
      }
    return $next($request);
    }
}
