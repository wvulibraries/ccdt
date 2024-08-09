<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Collection;

/**
 * Check Collection Id Middleware 
 *
 * checkcollectionid middleware used for checking for a 
 * valid collection id in routes. Currently used by the 
 * Upload Controller.
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
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
     * 
     * @param integer (collection id)
     * 
     * @return boolean
     */
    public function isValidCollection($colID) {
      if (!is_null($colID) && is_numeric($colID)) {
        return Collection::find($colID) == null ? false : true;
      }
      return false;
    }

    /**
     * The function returns true if
     * the hasAccess value of the collection
     * is set to 1. False otherwise.
     * 
     * @param integer (collection id)
     * 
     * @return boolean
     */
    public function hasAccess($colID) {
      if ($this->isValidCollection($colID)) {
        $collection = Collection::find($colID);
        if ($collection->hasAccess == 1) {
          return true;
        }
      }
      return false;
    }
}
