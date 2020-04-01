<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Http\Middleware;

use Closure;
use App\Models\Collection;

class CheckCollectionId {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
      if ($this->isValidCollection($request->curCol) && $this->hasAccess($request->curCol)) {
        return $next($request);
      }
      $message = !($this->isValidCollection($request->curCol)) ? 'Collection id is invalid' : 'Collection is disabled';
      return redirect()->route('home')->withErrors([ $message ]);
    }

    /**
    * The function will check if the passed id is valid using:
    * 1. Check for the null values
    * 2. Check for the non numeric values
    * 3. Check for the table id
    **/
    public function isValidCollection($curCol) {
      if (!is_null($curCol) && is_numeric($curCol)) {
        return Collection::find($curCol) == null ? false : true;
      }
      return false;
    }

    public function hasAccess($curCol) {
      if ($this->isValidCollection($curCol)) {
        $curCollection = Collection::find($curCol);
        if ($curCollection->hasAccess == 1) {
          return true;
        }
      }
      return false;
    }
}
