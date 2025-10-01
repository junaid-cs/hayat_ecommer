<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check())
        {
            if(Auth::user()->role !== '0')
            {
                return $next($request);
            }
            else
            {
                return redirect('/dashboard')->with('status','Access Denied! as you are not as admin');
            }
        }
        else
        {
            return redirect('/dashboard')->with('status','Please Login First');
        }
    }
}
