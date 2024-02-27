<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Cek_login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles): Response
    {
        //this check if user already login or no. if no user will return to login page
        if(!Auth::check()){
            return redirect('login');
        }

        //save user data to $user variable
        $user = Auth::user();

        //if user has role as admin or user, u will go to dashboard page
        if($user->level == $roles){
            return $next($request);
        }
        // if you don't have access, u will redirect to login page
        return redirect('login')->with('error', 'Maaf anda tidak memiliki akses');
    }
}
