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
      $rcrds = DB::table($curTable->tblNme)
                  ->where('id', '=', $curId)
                  ->get();
      // check for the number of records
      if (count ($rcrds) == 0){
        return redirect()->route('home')->withErrors(['Search Yeilded No Results']);
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

  public function search(Request $request, $curTable, $search = NULL){
    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);

    if(!$curTable->hasAccess){
      return redirect()->route('home')->withErrors(['Table is disabled']);
    }

    if ($search == NULL) {
      $search = $request->input('search');
    }

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);
    $perPage = 30;
    $tblNme = $curTable->tblNme;

    //set current page
    $pageStart = \Request::get('page', 1);

    // Searchy is returning a collection aka Array of Objects
    // Copy Column names
    $srcClmn = $clmnNmes;
    // Remove first item which is ID
    array_shift($srcClmn);
    
    $rcrds = \Searchy::$tblNme($srcClmn)
      ->query($search)
      ->getQuery()
      ->having('relevance', '>', 20)
      ->get();

    $rcrdsCount = $rcrds->count();

    // create array chunks of results for use in page views
    if ($rcrdsCount > $perPage) {
      $chunks = $rcrds->chunk(30);
      $chunks->toArray();
      $rcrds = $chunks[$pageStart];
    }

    // set $lastPage
    if ($rcrdsCount > $perPage) {
      $lastPage = ceil($rcrdsCount / $perPage);
    }
    else {
      $lastPage = $pageStart;
    }

    // return the index page
    return view('user.search')->with('rcrds', $rcrds)
                              ->with('clmnNmes', $clmnNmes)
                              ->with('tblNme', $curTable->tblNme)
                              ->with('tblId', $curTable)
                              ->with('search', $search)
                              ->with('page', $pageStart)
                              ->with('lastPage', $lastPage)
                              ->with('morepages', $pageStart < $lastPage);
  }

}
