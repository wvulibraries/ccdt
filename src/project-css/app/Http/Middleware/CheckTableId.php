<?php

namespace App\Http\Middleware;

use Closure;
use App\Table;

class CheckTableId
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
        // redirect is check fails
        if ($this->isValidTable($request->curTable)) {
            return $next($request);
        }

        return redirect()->route('home')->withErrors([ 'Table id is invalid' ]);
    }

    /**
    * The function will check if the passed id is valid using:
    * 1. Check for the null values
    * 2. Check for the non numeric values
    * 3. Check for the table id
    **/
    public function isValidTable($curTable) {
      if (is_null($curTable) || !is_numeric($curTable)) {
        return false;
      } else {
        return Table::find($curTable) == null ? false : true;
      }
    }
}
