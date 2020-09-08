<?php

namespace App\Http\Controllers;

use App\Jobs\FileImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\Table;
use App\Models\Collection;
use App\Models\CMSRecords;
use App\Helpers\CMSHelper;
use App\Helpers\CSVHelper;
use App\Helpers\CustomStringHelper;
use App\Helpers\TableHelper;

/**
 * The controller is responsible for showing, editing and updating the table(s)
 * 
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class TableController extends Controller
{
    private $strDir;
    private $extraClmns;

    /**
     * Create a new controller instance.
     *
     * @param string $storageFolder
     * 
     * @author Tracy A McCormick      
     * @return void
     */ 
    public function __construct($storageFolder = 'flatfiles') {
      // Protection to make sure this is only accessible to admin
      $this->middleware('admin');
      // Storage directory
      $this->strDir = $storageFolder;
      // offset for extra colmns
      $this->extraClmns = 3;
    }

    /**
     * Show the table index page
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */ 
    public function index() {
      // Get all the table records
      $tbls = Table::all();      
      // return the index page
      return view('admin/table')->with('tbls', $tbls);
    }

    /**
     * Render View that allows users to edit table
     *
     * @param integer $curTable
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */
    public function edit($curTable) {
      $table = Table::findOrFail($curTable);

      // Get the collection names
      $collcntNms = Collection::all();

      // redirect to show page
      return view('table/edit')->with('tblId', $table->id)
                               ->with('tblNme', $table->tblNme)
                               ->with('colID', $table->collection_id)
                               ->with('collcntNms', $collcntNms);
    }    

    /**
     * Takes form data and updates the table entry
     * then returns redirect to the edit schema 
     * page.   
     *
     * @param Request $request
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */    
    public function update(Request $request) {
      // get table from Tables
      $table = Table::findOrFail($request->tblId);
      $fieldUpdated = false;

      // Do not update unless the table name has changed
      if ($table->tblNme != $request->name) {
        // Verify new table name doesn't already exist
        if (Schema::hasTable($request->name)) {
          return redirect()->back()->with('error', ['The table name has already been taken by current or disabled table']); 
        }

        // Rename table
        Schema::rename($table->tblNme, $request->name);

        // Update entry in tables 
        $table->tblNme = $request->name;

        $fieldUpdated = true;
      }

      if ($table->collection_id != $request->colID) {      
        $table->collection_id = $request->colID;
        $fieldUpdated = true;
      }

      if ($fieldUpdated) {
        $table->save();
      }

      // redirect to edit schema page
      return redirect()->route('table.edit.schema',  ['curTable' => $request->tblId]);
    }
    
     /**
     * Gets table reads fields and determines field
     * types. Then renders the edit schema page.  
     *
     * @param integer $curTable
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */      
    public function editSchema($curTable) {
      // Set Empty Field Type Array
      $schema = [];

      // Get the table entry in meta table "tables"
      $table = Table::findOrFail($curTable);

      // Get Description of table
      $results = $table->getDescription();

      //loop over remaining table fields
      foreach ($results as $col) {
        // get varchar size
        preg_match_all('!\d+!', $col->Type, $size);
        if (count($size[0]) == 1) {
          $type = explode("(", $col->Type, 2);
          switch ($type[0]) {
              case 'varchar':
                  array_push($schema, [$col->Field => (new TableHelper)->setVarchar((int) $size[0][0])]);
                  break;
              case 'int':
              case 'mediumint':
              case 'bigint':
                  array_push($schema, [$col->Field => (new TableHelper)->setInteger($type[0])]);
                  break;                                      
          }
        }
        else {
          // if size isn't present then field is a text field
          array_push($schema, [$col->Field => (new TableHelper)->setText($col->Type)]);
        }
      }
      
      // return view
      return view('table.edit.schema')->with('tblId', $table->id)
                                      ->with('schema', $schema)
                                      ->with('tblNme', $table->tblNme)
                                      ->with('collctnId', $table->collection_id);
    }      

    /**
     * Takes form data and updates the table schema
     * then returns redirect to the collection show page.  
     *
     * @param Request $request
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */     
    public function updateSchema(Request $request) {
      //get table
      $table = Table::where('tblNme', $request->tblNme)->first();

      // Get Description of table
      $results = $table->getDescription();

      for ($i = 0; $i<$request->kCnt; $i++) {
        // Define current column name, type and size
        $curColNme = strval($request->{'col-'.$i.'-name'});
        
        // Ensure new column name is in proper format
        $curColNme = (new CustomStringHelper)->formatFieldName($curColNme);

        $curColType = strval($request->{'col-'.$i.'-data'});
        $curColSze = strval($request->{'col-'.$i.'-size'});

        $prevColNme = $results[$i]->Field;

        // If the Current and Previous Field Names are Different Update the Table
        if ($curColNme != $prevColNme) {
          Schema::table($request->tblNme, function($table) use ($prevColNme, $curColNme)
          {
              $table->renameColumn($prevColNme, $curColNme);
          });
        }

        (new TableHelper)->changeTableField($request->tblNme, $curColNme, $curColType, $curColSze);
      }

      // redirect to collection show page
      return redirect()->route('collection.show', ['colID' => $table->collection_id]);
    }

    /**
     * Return the admin/collection page to create tables 
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */  
    public function create() {
      // Get the collection names
      $collcntNms = Collection::all();

      // route to collection page to create tables
      if ($collcntNms->where('isEnabled', '1')->count()>0) {
        return view('admin/collection')->with('collcntNms', $collcntNms);
      }

      // route to collection page with errors if no collection exists or is active
      return view('admin/collection')->with('collcntNms', $collcntNms)->withErrors([ 'Please create active collection here first' ]);
    }

    /**
     * Method to format the storage files
     *
     * @param string $strDir
     * 
     * @author Tracy A McCormick
     * @return array 
     */ 
    public function getFiles($strDir) {
      // Get the list of files in the directory
      $fltFleList = Storage::allFiles($strDir);

      // Format the file names by truncating the dir
      foreach ($fltFleList as $key => $value) {
        // check if directory string exists in the path
        if (str_contains($value, $strDir.'/')) {
          // replace the string
          $fltFleList[ $key ] = str_replace($strDir.'/', '', $value);
        }
      }

      // return the list
      return $fltFleList;
    }

    /**
     * Method responsible to load the data into the given table
     *
     * @param boolean $isFrwded
     * @param string $tblNme
     * @param string $fltFle
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */  
    public function load($isFrwded = False, $tblNme = "", $fltFle = "") {
      // Check if the request is forwarded
      if ($isFrwded) {
        // Forward the file and table name
        $tblNms = Table::where('tblNme', $tblNme)->get();
        $fltFleList = array($fltFle);
      } else {
        // Get all the tables
        $tblNms = Table::all();
        // Get the list of files in the directory
        $fltFleList = $this->getFiles($this->strDir);
      }

      // Compact them into one array
      $ldData = array(
        'tblNms' => $tblNms,
        'fltFleList' => $fltFleList
      );

      // Simple return the value
      return view('admin.load')->with($ldData);
    }

    /**
     * Store takes a requested file import and adds it to the job queue for later processing
     *
     * @param Request $request
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */  
    public function store(Request $request) {
      // validate the file name and table name
      // Rules for validation
      $rules = array(
        'fltFle' => 'required|string',
        'tblNme' => 'required|string'
      );

      // Validate the request before storing the job
      $this->validate($request, $rules);

      // Queue Job for Import
      (new TableHelper)->dispatchImportJob($request->tblNme, $this->strDir, $request->fltFle);

      return redirect()->route('tableIndex');
    }

    /**
     * Disable the given table
     *
     * @param Request $request
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */  
    public function restrict(Request $request) {
      // Create the collection name
      $thisTbl = Table::findOrFail($request->id);
      $thisTbl->hasAccess = false;
      $thisTbl->save();
      return redirect()->route('tableIndex');
    }

}
