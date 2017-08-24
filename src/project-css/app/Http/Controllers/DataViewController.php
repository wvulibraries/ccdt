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
    // set records per page
    $perPage = 30;

    $string_helper = new customStringHelper();

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

    $cleanString = $string_helper->cleanSearchString($search);

    $rcrds = DB::table($tblNme)
                 ->whereRaw("MATCH (srchindex) AGAINST (? IN BOOLEAN MODE)", $cleanString)
                 ->orderBy('id', 'asc')
                 ->offset($page-1 * $perPage)
                 ->limit($perPage)
                 ->get();

    // query sorted by revelancy score
    // $query = DB::table($tblNme)
    //         ->whereRaw("match(srchindex) against (? in boolean mode)", [$cleanString])
    //         ->orderBy('score', 'desc')
    //         ->offset($page-1 * $perPage)
    //         ->limit($perPage);
    //
    // $rcrds = $query
    //         ->get(['*', DB::raw("MATCH (srchindex) AGAINST ('".$cleanString."') AS score")]);

    $rcrdsCount = count($rcrds);
    // var_dump($rcrds);
    // var_dump($rcrdsCount);
    // die();

    // if last query returned exactly 30 items
    // we assume that their are additional pages
    // so we set $lastPage to $page + 1
    if ($rcrdsCount == $perPage) {
      $lastPage = $page + 1;
    }
    else {
      $lastPage = $page;
    }

    return view('user.search')->with('rcrds', $rcrds)
                              ->with('clmnNmes', $clmnNmes)
                              ->with('tblNme', $curTable->tblNme)
                              ->with('tblId', $curTable)
                              ->with('search', $search)
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

}
