<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::user()->role){
            $roles = array_slice(func_get_args(), 1);
            foreach($roles as $role){
                $user = Auth::user()->role;
                if($user == $role){
                    return $next($request);
                }
            }
            return redirect()->route('login');
        }else{
            return redirect()->route('login');
        }
    }

}