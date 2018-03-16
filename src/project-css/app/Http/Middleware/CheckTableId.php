<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Table;

class CheckTableId {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
      if ($this->isValidTable($request->curTable) && $this->hasAccess($request->curTable)) {
        return $next($request);
      }
      $message = !($this->hasAccess($request->curTable)) ? 'Table is disabled' : 'Table is invalid';
      return redirect()->route('home')->withErrors([ $message ]);
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

    public function hasAccess($curTable) {
      // Get the table entry in meta table "tables"
      $curTable = Table::find($curTable);
      return $curTable->hasAccess;
    }
}
