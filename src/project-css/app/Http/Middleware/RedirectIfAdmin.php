<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class RedirectIfAdmin
{
    /**
     * Handle an incoming request and checks if the
     * user is admin
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        // Check if the user is logged in
        if(Auth::check()){
        // Check if the user is admin
        if($request->user()->isAdmin){
            return $next($request);
        }
      }

        // Else just redirect him to the home page
        return redirect('home');
    }
}
