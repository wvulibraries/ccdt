<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Table;
use App\Collection;
use App\Libraries\CustomStringHelper;
use App\Libraries\ParseWordDocuments;
use PhpOffice\PhpWord\IOFactory;

/**
* The controller is responsible for showing the cards data
*/
class DataViewController extends Controller{

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
  public function index($curTable){
      // test for the validity of curtable
      if(!$this->isValidTable($curTable)){
        return redirect()->route('home')->withErrors(['Table id is invalid']);
      }

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

  /**
  * Show a record in the table
  */
  public function show($curTable, $curId){
    // test for the validity of curtable
    if(!$this->isValidTable($curTable)){
      return redirect()->route('home')->withErrors(['Table id is invalid']);
    }

    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);

    if(!$curTable->hasAccess){
      return redirect()->route('home')->withErrors(['Table is disabled']);
    }

    // Check if id
    if (is_null($curId) || !is_numeric($curId)){
      return redirect()->route('home')->withErrors(['Invalid ID']);
    }

    // query database for record
    $rcrds = DB::table($curTable->tblNme)
                ->where('id', '=', $curId)
                ->get();

    // check for the number of records if their is none return with error message
    if (count ($rcrds) == 0){
      return redirect()->route('home')->withErrors(['Search Yeilded No Results']);
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

    if ($search == NULL){
      $search = $request->input('search');
    }

    $srchStrng = (new customStringHelper)->cleanSearchString($search);

    // set records per page
    $perPage = 30;

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

    // query sorted by revelancy score
    $query = DB::table($curTable->tblNme)
            ->whereRaw("match(srchindex) against (? in boolean mode)", [$srchStrng])
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
      return redirect()->route('home')->withErrors(['Table id is invalid']);
    }
    
    // Get the table entry in meta table "tables"
    $curTable = Table::find($curTable);

    if(!$curTable->hasAccess){
      return redirect()->route('home')->withErrors(['Table is disabled']);
    }

    // retrieve the column names
    $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

    $path = storage_path('app/' . $curTable->tblNme . '/' . $subfolder . '/' . $filename);

    $fileMimeType = Storage::getMimeType($curTable->tblNme . '/' . $subfolder . '/' . $filename);

    switch ($fileMimeType) {
        case 'text/plain':
        case 'message/rfc822':
             $fileContents = file_get_contents($path);
             return Response::make($fileContents);
             break;
        case 'application/msword':
             $fileContents = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","", (new ParseWordDocuments)->parseDoc($path));
             return Response::make($fileContents);
             break;

       case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
             $fileContents = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","", (new ParseWordDocuments)->parseDocx($path));
             return Response::make($fileContents);
             break;


           // convert to html with phpword
          //  $phpWord = IOFactory::load($path, 'Word2007');
          //  $fileContents = IOFactory::createWriter($phpWord, 'HTML');
          //  return view('user.fileviewer')->with('tblId', $curTable)
          //                                ->with('fileMimeType', $fileMimeType)
          //                                ->with('fileContents', $fileContents)
          //                                ->with('fileName', $filename);
          // break;

        default:
           // download file if we cannot determine what kind of file it is.
           return Response::make(file_get_contents($path), 200, [
              'Content-Type' => Storage::getMimeType($curTable->tblNme . '/' . $subfolder . '/' . $filename),
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
  public function isValidTable($curTable){
    if (is_null($curTable) || !is_numeric($curTable)){
      return false;
    } else {
      $tableExists = Table::find($curTable) == null ? false : true;
      return $tableExists;
    }
  }

}
