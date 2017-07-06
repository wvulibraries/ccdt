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

  public function search(Request $request, $curTable, $search = NULL, $page = 1, $driver = NULL, $column = NULL, $cache = NULL){
    // set records per page
    $perPage = 30;
    $relevance = 20;
    $limit = 1000;

    //set current page
    //$page = \Request::get('page', 1);

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

    if ($driver == NULL) {
      $driver = \Request::get('driver', 'fuzzy');
    }

    if ($column == NULL) {
      $column = \Request::get('search-col', 'srchindex');
    }

    if ($cache == NULL) {
      $cache = \Request::get('cache', 'false');
    }

    // echo ('<pre>');
    // var_dump($search);
    // var_dump($page);
    // var_dump($driver);
    // var_dump($column);
    // var_dump($cache);
    // echo ('</pre>');
    // die();

    $startTime = microtime(true);

    // create array chunks of results for use in page views
    $file = fopen("microtime.log","a");

    if (\Cache::has($driver . $tblNme . $column . $search . $page) && (strcmp($cache, 'true') == 0))
    {
      $rcrdsCount = \Cache::get($driver . $tblNme . $column . $search);
      $rcrds = \Cache::get($driver . $tblNme . $column . $search . $page);
      fwrite($file,"Cached Search - Driver " . $driver . " Column " . $column . " Search " . $search . " Table " . $tblNme . " ");
    }
    else {
      // Searchy is returning a collection aka Array of Objects
      $rcrds = \Searchy::driver($driver)
        ->$tblNme($column)
        ->query($search)
        ->getQuery()
        ->limit($limit)
        ->having('relevance', '>', $relevance)
        ->get();

      $rcrdsCount = $rcrds->count();

      if ($rcrdsCount > $perPage) {
        $chunks = $rcrds->chunk(30);
        $chunks->toArray();
        if (strcmp($cache, 'true') == 0) {
          \Cache::put($driver . $tblNme . $column . $search, $rcrdsCount, 60);
          foreach($chunks as $key => $chunk) {
            \Cache::put($driver . $tblNme . $column . $search . $key, $chunk, 60);
          }
        }
        $rcrds = $chunks[$page];
      }
      elseif (strcmp($cache, 'true') == 0) {
        \Cache::put($driver . $tblNme . $column . $search, $rcrdsCount, 60);
        \Cache::put($driver . $tblNme . $column . $search . '1', $rcrds, 60);
      }

      fwrite($file,"Normal Search - Driver " . $driver . " Column " . $column . " Search " . $search . " Table " . $tblNme . " ");
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
                              ->with('page', $page)
                              ->with('lastPage', $lastPage)
                              ->with('morepages', $page < $lastPage)
                              ->with('driver', $driver)
                              ->with('column', $column)
                              ->with('cache', $cache);
  }

}
