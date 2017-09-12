<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;

// Import the storage class too
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;

// Import the table and collection models
use App\Table;
use App\Collection;
use Illuminate\Support\Facades\Auth;

use App\Libraries\CustomStringHelper;

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

  public function search(Request $request, $curTable, $search = NULL, $page = 1){
    // test for the validity of curtable
    if(!$this->isValidTable($curTable)){
      return redirect()->route('home')->withErrors(['Table id is invalid']);
    }

    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);
    if(!$curTable->hasAccess){
      return redirect()->route('home')->withErrors(['Table is disabled']);
    }

    if ($search == NULL) {
      $search = $request->input('search');
    }

    $string_helper = new customStringHelper();
    $srchStrng = $string_helper->cleanSearchString($search);

    // set records per page
    $perPage = 30;

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

    $tblNme = $curTable->tblNme;

    // query sorted by revelancy score
    $query = DB::table($tblNme)
            ->whereRaw("match(srchindex) against (? in boolean mode)", [$srchStrng])
            ->orderBy('score', 'desc')
            ->offset($page-1 * $perPage)
            ->limit($perPage);

    $rcrds = $query
            ->get(['*', DB::raw("MATCH (srchindex) AGAINST ('".$srchStrng."') AS score")]);

    $rcrdsCount = count($rcrds);

    // if fulltext search yeilds no results try a basic like search
    if ($rcrdsCount == 0) {

      $offsetNum = ($page == 1) ? 0 : ($page-1) * $perPage;

      $query = DB::table($tblNme)
              ->where('srchindex', 'like', '%' . $srchStrng . '%')
              ->orderBy('id', 'asc')
              ->offset($offsetNum)
              ->limit($perPage);

      $rcrds = $query
              ->get();

      $rcrdsCount = count($rcrds);
    }

    // if last query returned exactly 30 items
    // we assume that their are additional pages
    // so we set $lastPage to $page + 1
    $lastPage = ($rcrdsCount == $perPage) ? $page + 1 : $page;


    return view('user.search')->with('rcrds', $rcrds)
                              ->with('clmnNmes', $clmnNmes)
                              ->with('tblNme', $curTable->tblNme)
                              ->with('tblId', $curTable)
                              ->with('search', $srchStrng)
                              ->with('page', $page)
                              ->with('lastPage', $lastPage)
                              ->with('morepages', $page < $lastPage);
  }

  public function view(Request $request, $curTable, $subfolder, $filename){
    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);

    if(!$curTable->hasAccess){
      return redirect()->route('home')->withErrors(['Table is disabled']);
    }

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

    $path = storage_path('app/' . $curTable->tblNme . '/' . $subfolder . '/' . $filename);

    return Response::make(file_get_contents($path), 200, [
        'Content-Type' => Storage::getMimeType($curTable->tblNme . '/' . $subfolder . '/' . $filename),
        'Content-Disposition' => 'inline; filename="'.$filename.'"'
    ]);
  }

  /**
  * The function will check if the passed id is valid using:
  * 1. Check for the null values
  * 2. Check for the non numeric values
  * 3. Check for the table id
  **/
  public function isValidTable($curTable){
    if(is_null($curTable) || !is_numeric($curTable) || !$this->isCurTableExists($curTable)){
      return false;
    }
    return true;
  }

  // Check weather the table with id=curTable exists
  public function isCurTableExists($curTable){
    try{
      // returns exception if doesn't exists
      $curTable = Table::findorFail($curTable);
    } catch (\Exception $e){
      return false;
    }
    return true;
  }

}
