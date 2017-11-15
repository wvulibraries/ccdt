<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Table;
use App\Libraries\CustomStringHelper;
use App\Libraries\TikaConvert;

/**
* The controller is responsible for showing the cards data
*/
class DataViewController extends Controller {
  // various error messages
  public $tableIdErr = 'Table id is invalid';
  public $tableDisabledErr = 'Table is disabled';
  public $tableNoRecordsErr = 'Table does not have any records.';
  public $invalidRecordIdErr = 'Invalid Record ID';
  public $noResultsErr = 'Search Yeilded No Results';

  /**
   * Constructor that associates the middlewares
   */
  public function __construct(){
    // Middleware to check for authenticated
    $this->middleware('auth');
  }

  /**
  * Show the data from the selected table
  */
  public function index($curTable) {
      // test for the validity of curtable
      if (!$this->isValidTable($curTable)) {
        return redirect()->route('home')->withErrors([ $this->tableIdErr ]);
      }

      // Get the table entry in meta table "tables"
      $curTable = Table::find($curTable);
      if(!$curTable->hasAccess){
        return redirect()->route('home')->withErrors([ $this->tableDisabledErr ]);
      }

      // Get and return of table doesn't have any records
      $numOfRcrds = DB::table($curTable->tblNme)->count();
      // check for the number of records
      if ($numOfRcrds == 0) {
        return redirect()->route('home')->withErrors([ $this->tableNoRecordsErr ]);
      }

      // Get the records 30 at a time
      $rcrds = DB::table($curTable->tblNme)->paginate(30);

      // retrieve the column names
      $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

      // return the index page
      return view('user.data')->with('rcrds', $rcrds)
                              ->with('clmnNmes', $clmnNmes)
                              ->with('tblNme', $curTable->tblNme)
                              ->with('tblId', $curTable);

  }

  /**
  * Show a record in the table
  */
  public function show($curTable, $curId) {
    // test for the validity of curtable
    if (!$this->isValidTable($curTable)) {
      return redirect()->route('home')->withErrors([ $this->tableIdErr ]);
    }

    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);

    if(!$curTable->hasAccess) {
      return redirect()->route('home')->withErrors([ $this->tableDisabledErr ]);
    }

    // Check if id is valid
    if (is_null($curId) || !is_numeric($curId)) {
      return redirect()->route('home')->withErrors([ $this->invalidRecordIdErr ]);
    }

    // query database for record
    $rcrds = DB::table($curTable->tblNme)
                ->where('id', '=', $curId)
                ->get();

    // check for the number of records if their is none return with error message
    if (count ($rcrds) == 0) {
      return redirect()->route('home')->withErrors([ $this->noResultsErr ]);
    }

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

    // return the index page
    return view('user.show')->with('rcrds', $rcrds)
                            ->with('clmnNmes', $clmnNmes)
                            ->with('tblNme', $curTable->tblNme)
                            ->with('tblId', $curTable);
  }

  public function search(Request $request, $curTable, $search = NULL, $page = 1) {
    // test for the validity of curtable
    if(!$this->isValidTable($curTable)) {
      return redirect()->route('home')->withErrors([ $this->tableIdErr ]);
    }

    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);
    if(!$curTable->hasAccess) {
      return redirect()->route('home')->withErrors([ $this->tableDisabledErr ]);
    }

    if ($search == NULL) {
      $search = $request->input('search');
    }

    $srchStrng = (new customStringHelper)->cleanSearchString($search);

    // set records per page
    $perPage = 30;

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

    // query sorted by revelancy score
    $query = DB::table($curTable->tblNme)
            ->whereRaw("match(srchindex) against (? in boolean mode)", [ $srchStrng ])
            ->orderBy('score', 'desc')
            ->offset($page-1 * $perPage)
            ->limit($perPage);

    $rcrds = $query
            ->get(['*', DB::raw("MATCH (srchindex) AGAINST ('".$srchStrng."') AS score")]);

    $rcrdsCount = count($rcrds);

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

  public function view($curTable, $subfolder, $filename){
    // test for the validity of curtable
    if(!$this->isValidTable($curTable)){
      return redirect()->route('home')->withErrors([ $this->tableIdErr ]);
    }

    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);

    if(!$curTable->hasAccess){
      return redirect()->route('home')->withErrors([ $this->tableDisabledErr ]);
    }

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

    $source = storage_path('app/'.$curTable->tblNme.'/'.$subfolder.'/'.$filename);

    $fileMimeType = Storage::getMimeType($curTable->tblNme.'/'.$subfolder.'/'.$filename);

    $matches = "/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/";

    switch ($fileMimeType) {
        case 'text/plain':
        case 'message/rfc822':
             return Response::make((new customStringHelper)->ssnRedact(file_get_contents($source)));
             break;
       case 'application/msword':
       case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
       case 'text/rtf':
             $fileContents = preg_replace($matches,"", (new tikaConvert)->convert($source));
             return Response::make((new customStringHelper)->ssnRedact($fileContents));
             break;
       default:
             // download file if we cannot determine what kind of file it is.
             return Response::make(file_get_contents($source), 200, [
                'Content-Type' => Storage::getMimeType($curTable->tblNme.'/'.$subfolder.'/'.$filename),
                'Content-Disposition' => 'inline; filename="'.$filename.'"'
            ]);
    }
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
