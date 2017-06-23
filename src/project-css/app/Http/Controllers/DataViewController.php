<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Import the table and collection models
use App\Table;
use App\Collection;
use Illuminate\Support\Facades\Auth;

/**
* The controller is responsible for showing the cards data
*/
class DataViewController extends Controller {

  /**
   * Constructor that associates the middlewares
   *
   * @return void
   */
  public function __construct(){
    // Middleware to check for authenticated
    $this->middleware('auth');
  }

  /**
  * Show the data from the selected table
  */
  public function index(Request $request, $curTable){
    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);
    if(!$curTable->hasAccess){
      return redirect()->route('home')->withErrors(['Table is disabled']);
    }

    // Get and return of table doesn't have any records
    $numOfRcrds = DB::table($curTable->tblNme)->count();
    // check for the number of records
    if ($numOfRcrds == 0){
      return redirect()->route('home')->withErrors(['Table does not have any records.']);
    }

    // Get the records 30 at a time
    $rcrds = DB::table($curTable->tblNme)->paginate(30);

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

    // return the index page
    return view('user.data')->with('rcrds',$rcrds)
                            ->with('clmnNmes',$clmnNmes)
                            ->with('tblNme',$curTable->tblNme)
                            ->with('tblId',$curTable);
  }

  public function show(Request $request, $curTable, $curId){
    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);
    if(!$curTable->hasAccess){
      return redirect()->route('home')->withErrors(['Table is disabled']);
    }

    // Check if search string and column were passed
    if (strlen($curId) != 0) {
      $numOfRcrds = DB::table($curTable->tblNme)
                      ->where('id', 'LIKE', $curId)
                      ->count();
      // check for the number of records
      if ($numOfRcrds == 0){
        return redirect()->route('home')->withErrors(['Search Yeilded No Results']);
      }
      else {
        $rcrds = DB::table($curTable->tblNme)
                    ->where('id', 'LIKE', $curId)
                    ->get();
      }
    }
    else {
      return redirect()->route('home')->withErrors(['Invalid ID']);
    }

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

    // return the index page
    return view('user.show')->with('rcrds',$rcrds)
                            ->with('clmnNmes',$clmnNmes)
                            ->with('tblNme',$curTable->tblNme)
                            ->with('tblId',$curTable);
  }

}
