<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

/**
 * RedirectIfAdmin Middleware 
 *
 * RedirectIfAdmin middleware used for checking if the
 * current user is a admin. Can be used to in controlling
 * access to pages that are admin only. If a non admin tries
 * to access the route they are redirected to the home page.
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
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
    public function handle($request, Closure $next) {
      // Check if the user is logged in
      if (Auth::check()) {
        // Check if the user is admin
        if ($request->user()->isAdmin) {
            return $next($request);
        }
      }
      // Else just redirect him to the home page
      return redirect('home');
    }
}
