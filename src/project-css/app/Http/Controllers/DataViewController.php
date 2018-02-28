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
    public $tableDisabledErr = 'Table is disabled';
    public $tableNoRecordsErr = 'Table does not have any records.';
    public $invalidRecordIdErr = 'Invalid Record ID';
    public $noResultsErr = 'Search Yeilded No Results';
    public $invalidTableErr = 'Invalid Table ID';
    public $invalidSearchStrErr = 'Invalid Search String';

    /**
     * Constructor that associates the middlewares
     */
    public function __construct() {
        // Middleware to check for authenticated
        $this->middleware('auth');
    }

    /**
     * Show the data from the selected table
     */
    public function index($curTable) {
        // Get the table entry in meta table "tables"
        $curTable = Table::find($curTable);
        if (!$curTable->hasAccess) {
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
        // Get the table entry in meta table "tables"
        $curTable = Table::find($curTable);

        if (!$curTable->hasAccess) {
           return redirect()->route('home')->withErrors([ $this->tableDisabledErr ]);
        }

        // Check if id is valid
        if (is_null($curId) || !is_numeric($curId)) {
           return redirect()->back()->withErrors([ $this->invalidRecordIdErr ]);
        }

        // query database for record
        $rcrds = DB::table($curTable->tblNme)
                    ->where('id', '=', $curId)
                    ->get();

        // check for the number of records if their is none return with error message
        if (count($rcrds) == 0) {
           return redirect()->back()->withErrors([ $this->noResultsErr ]);
        }

        // retrieve the column names
        $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

        // return the index page
        return view('user.show')->with('rcrds', $rcrds)
                                ->with('clmnNmes', $clmnNmes)
                                ->with('tblNme', $curTable->tblNme)
                                ->with('tblId', $curTable)
                                ->with('curId', $curId);
    }

    public function search(Request $request, $curTable, $search = NULL, $page = 1) {
        // Get the table entry in meta table "tables"
        $curTable = Table::find($curTable);
        if (!$curTable->hasAccess) {
           return redirect()->back()->withErrors([ $this->tableDisabledErr ]);
        }

        if ($search == NULL) {
          $search = $request->input('search');
        }

        $srchStrng = (new customStringHelper)->cleanSearchString($search);

        // set records per page
        $perPage = 30;

        // retrieve the column names
        $clmnNmes = DB::getSchemaBuilder()->getColumnListing($curTable->tblNme);

        try {
          $query = DB::table($curTable->tblNme)
                  ->whereRaw("match(srchindex) against (? in boolean mode)", [ $srchStrng, $srchStrng ])
                  ->orderBy('score', 'desc')
                  ->offset($page - 1 * $perPage)
                  ->limit($perPage);
        } catch(\Illuminate\Database\QueryException $ex){
          return redirect()->back()->withErrors([ $this->invalidSearchStrErr ]);
        }

        try {
          $rcrds = $query->get([ '*', DB::raw("MATCH (srchindex) AGAINST (?) AS score")]);
        } catch(\Illuminate\Database\QueryException $ex){
          return redirect()->back()->withErrors([ $this->invalidSearchStrErr ]);
        }

        $rcrdsCount = count($rcrds);

        // if last query returned exactly 30 items
        // we assume that their are additional pages
        // so we set $lastPage to $page + 1
        $lastPage = ($rcrdsCount == $perPage) ? $page + 1 : $page;

        if ($rcrdsCount == 0) {
          return view('user.search')->with('tblId', $curTable)
                                    ->with('tblNme', $curTable->tblNme)
                                    ->with('page', $page)
                                    ->with('lastPage', $lastPage)
                                    ->with('rcrds', $rcrds)
                                    ->withErrors([ $this->noResultsErr ]);
        }

        return view('user.search')->with('rcrds', $rcrds)
                                  ->with('clmnNmes', $clmnNmes)
                                  ->with('tblNme', $curTable->tblNme)
                                  ->with('tblId', $curTable)
                                  ->with('search', $srchStrng)
                                  ->with('page', $page)
                                  ->with('lastPage', $lastPage)
                                  ->with('morepages', $page<$lastPage);
    }


    /**
     * view checks the file type and if its plain text or a word document it
     * will run ssnRedact to replace a US style social security number with
     * ###-##-####. Any other files the user will be able to download and then
     * view with a local application.
     */
    public function view($curTable, $recId, $subfolder, $filename) {
        // Get the table entry in meta table "tables"
        $curTable = Table::find($curTable);

        if (!$curTable->hasAccess) {
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
                 $fileContents = Response::make((new customStringHelper)->ssnRedact(file_get_contents($source)));
                 return view('user.fileviewer')->with('fileContents', $fileContents)
                                               ->with('tblNme', $curTable->tblNme)
                                               ->with('tblId', $curTable)
                                               ->with('recId', $recId);
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'text/rtf':
                 $fileContents = preg_replace($matches, "", (new tikaConvert)->convert($source));
                 $fileContents = Response::make((new customStringHelper)->ssnRedact($fileContents));
                 return view('user.fileviewer')->with('fileContents', $fileContents)
                                               ->with('tblNme', $curTable->tblNme)
                                               ->with('tblId', $curTable)
                                               ->with('recId', $recId);
            default:
                 // download file if we cannot determine what kind of file it is.
                 return Response::make(file_get_contents($source), 200, [
                    'Content-Type' => Storage::getMimeType($curTable->tblNme.'/'.$subfolder.'/'.$filename),
                    'Content-Disposition' => 'inline; filename="'.$filename.'"'
                ]);
        }
    }

    public function isValidTable($tblId) {
        // Get the table entry in meta table "tables"
        $curTable = Table::find($tblId);
        if ($curTable != NULL) {
           return (true);
        }
        return (false);
    }

}
