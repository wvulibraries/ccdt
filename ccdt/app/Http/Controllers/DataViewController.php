<?php

namespace App\Http\Controllers;

use Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Table;
use App\Libraries\FullTextSearchFormatter;
use App\Helpers\CustomStringHelper;
use App\Helpers\FileViewHelper;
use App\Helpers\TableHelper;

/**
 * The controller is responsible for showing the cards data
 * 
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class DataViewController extends Controller {
    // various error messages
    public $tableNoRecordsErr = 'Table does not have any records.';
    public $invalidRecordIdErr = 'Invalid Record ID';
    public $noResultsErr = 'Search Yeilded No Results';
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
     * 
     * @param string $curTable - id of the table
     *        
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response ( returns a view of records with pagination (30 records at a time) )
     */      
    public function index($curTable) {
        $table = Table::findOrFail($curTable);

        // check for the number of records
        if ($table->recordCount() == 0) {
          return redirect()->route('home')->withErrors([ $this->tableNoRecordsErr ]);
        }

        // return the index page
        return view('user.data')->with('rcrds', $table->getPage(30))
                                ->with('clmnNmes', $table->getColumnList())
                                ->with('tblNme', $table->tblNme)
                                ->with('tblId', $curTable);
    }

    /**
     * Show a record in the table
     * 
     * @param string $curTable - id of the table
     * @param integer $curId - the record id 
     *        
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response ( returns a view showing requested record )
     */      
    public function show($curTable, $curId) {
        // Check if id is valid
        if (is_null($curId) || !is_numeric($curId)) {
           return redirect()->back()->withErrors([ $this->invalidRecordIdErr ]);
        }

        // Get the table entry in meta table "tables"
        $table = Table::findOrFail($curTable);

        // query database for record
        $rcrds = $table->getRecord($curId);

        // check for the number of records if their is none return with error message
        if (count($rcrds) == 0) { return redirect()->back()->withErrors([ $this->noResultsErr ]); }

        // return the index page
        return view('user.show')->with('rcrds', $rcrds)
                                ->with('clmnNmes', $table->getColumnList())
                                ->with('tblNme', $table->tblNme)
                                ->with('tblId', $curTable)
                                ->with('curId', $curId);
    }

    /**
     * Uses Mysql full text search to query the database table
     * function returns results 30 records at a time.
     * 
     * @param request $request
     * @param string $curTable - id of the table
     * @param integer $page - page number to be displayed 
     *        
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response (returns a view with found records)
     */       
    public function search(Request $request, $curTable, $page = 1) {
        // Get the table entry in meta table "tables"
        $table = Table::findOrFail($curTable);

        // save the search to the session if it isn't NULL
        if ($request->input('search') != NULL) {
          $request->session()->put('search', $request->input('search'));
        }

        // pull search from session and prepare search
        $srchStrng = (new FullTextSearchFormatter)->prepareSearch($request->session()->get('search'));

        // set records per page
        $perPage = 30;

        // perform full text query 
        $rcrds = $table->fullTextQuery($srchStrng, $page, $perPage);

        // get number of records returned
        $rcrdsCount = count($rcrds);

        // if last query returned exactly 30 items
        // we assume that their are additional pages
        // so we set $lastPage to $page + 1
        $lastPage = ($rcrdsCount == $perPage) ? $page + 1 : $page;

        // return view with errors if no records are found
        if ($rcrdsCount == 0) {
          return view('user.search')->with('tblId', $curTable)
                                    ->with('tblNme', $table->tblNme)
                                    ->with('page', $page)
                                    ->with('lastPage', $lastPage)
                                    ->with('rcrds', $rcrds)
                                    ->withErrors([ $this->noResultsErr ]);
        }

        return view('user.search')->with('rcrds', $rcrds)
                                  ->with('clmnNmes', $table->getColumnList())
                                  ->with('tblNme', $table->tblNme)
                                  ->with('tblId', $curTable)
                                  ->with('page', $page)
                                  ->with('lastPage', $lastPage)
                                  ->with('morepages', $page<$lastPage);
    }

    /**
     * view checks the file type and if its plain text or a word document it
     * will run ssnRedact to replace a US style social security number with
     * ###-##-####. Any other files the user will be able to download and then
     * view with a local application.
     * 
     * @param string $curTable - id of the table
     * @param integer $recId - the record id 
     * @param string $filename - requested file to be viewed
     *        
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response ( generated view or generates a response to display raw file )
     */  
    public function view($curTable, $recId, $filename) {
        // Get the table entry in meta table "tables"
        $table = Table::findOrFail($curTable);

        $path = (new FileViewHelper)->getFilePath($curTable, $recId, $filename);

        $source = storage_path('app/'.$path);
        $fileMimeType = Storage::getMimeType($path);

        if ((new FileViewHelper)->isSupportedMimeType($fileMimeType)) {
          return view('user.fileviewer')->with('fileContents', (new FileViewHelper)->getFileContents($source))
                                        ->with('tblNme', $table->tblNme)
                                        ->with('tblId', $curTable)
                                        ->with('recId', $recId);
        }

        // download file so user can view if tika doesn't support 
        // the conversion to text
        return Response::make(file_get_contents($source), 200, [
           'Content-Type' => $fileMimeType,
           'Content-Disposition' => 'inline; filename="'.$filename.'"'
       ]);
    }

}
