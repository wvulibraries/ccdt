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
    // set records per page
    $perPage = 30;
    $relevance = 20;
    $limit = 1000;

    //set current page
    $pageStart = \Request::get('page', 1);

    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);

    if(!$curTable->hasAccess){
      return redirect()->route('home')->withErrors(['Table is disabled']);
    }

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

    $tblNme = $curTable->tblNme;

    if ($search == NULL) {
      $search = $request->input('search');
    }

    // check for the presence of the srchindex column
    // set $srcClmns to only search 'srchindex' if found
    if (in_array("srchindex", $clmnNmes)) {
      $srcClmns = "srchindex";
    }
    else {
      // Copy Column names
      $srcClmns = $clmnNmes;
      // Remove first item which is ID
      array_shift($srcClmns);
      // pick first 5 columns only
      //$srcClmn = array_slice($srcClmn, 0, 5, true);
    }

    $startTime = microtime(true);

    // create array chunks of results for use in page views
    $file = fopen("microtime.log","a");

    if (\Cache::has($search . $pageStart))
    {
      $rcrdsCount = \Cache::get($search);
      $rcrds = \Cache::get($search . $pageStart);
      fwrite($file,"Cache - Searchy - Search " . $search . " " . $tblNme . " ");
    }
    else {
      \Cache::flush();
      // Searchy is returning a collection aka Array of Objects
      $rcrds = \Searchy::$tblNme($srcClmns)
        ->query($search)
        ->getQuery()
        ->limit($limit)
        ->having('relevance', '>', $relevance)
        ->get();

      $rcrdsCount = $rcrds->count();
      \Cache::put($search, $rcrdsCount, 60);
      if ($rcrdsCount > $perPage) {
        $chunks = $rcrds->chunk(30);
        $chunks->toArray();
        foreach($chunks as $key => $chunk) {
          \Cache::put($search . $key, $chunk, 60);
        }
        $rcrds = $chunks[$pageStart];
      }
      else {
        \Cache::put($search . '1', $rcrds, 60);
      }

      fwrite($file,"Normal - Searchy - Search " . $search . " " . $tblNme . " ");
    }

    $secs = microtime(true)-$startTime;
    fwrite($file, number_format($secs,3) . "\n");
    fclose($file);

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
