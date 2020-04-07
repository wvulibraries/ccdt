<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

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
use App\Libraries\CustomStringHelper;
use App\Libraries\CSVHelper;
use App\Libraries\CMSHelper;
use App\Libraries\TableHelper;

class TableController extends Controller
{
    private $strDir;
    private $extraClmns;
    public $lastErrRow;
    public $savedTkns;

    /**
     * Create a new controller instance.
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
    */
    public function index() {
      // Get all the table records
      $tbls = Table::all();      
      // return the index page
      return view('admin/table')->with('tbls', $tbls);
    }

    /**
    * Render View that allows users to edit table
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
    
    public function setVarchar($size) {
      switch ($size) { 
        case $size <= 30:
            return (['type' => 'string', 'size' => 'default']); 
            break;                     
        case $size <= 150:
            return (['type' => 'string', 'size' => 'medium']);
            break;
        default:
            return (['type' => 'string', 'size' => 'big']);
      }
    }
 
    public function setInteger($type) {
      switch ($type) {
        case 'mediumint':
          return (['type' => 'integer', 'size' => 'medium']);
          break;
        case 'bigint':
          return (['type' => 'integer', 'size' => 'big']); 
          break;
        default:  
          return (['type' => 'integer', 'size' => 'default']);      
      }      
    }    

    public function setText($type) {
      switch ($type) {
        case 'mediumtext':  
          return (['type' => 'text', 'size' => 'medium']);
          break;
        case 'longtext':
          return (['type' => 'text', 'size' => 'big']);
          break;
        default:
          return (['type' => 'text', 'size' => 'default']);
      }      
    }      

    public function editSchema($curTable) {
      // Set Empty Field Type Array
      $schema = [];

      // Get the table entry in meta table "tables"
      $table = Table::findOrFail($curTable);

      // Get Description of table
      $results = DB::select("DESCRIBE ".$table->tblNme);

      // remove first item we do not change the id of the table
      unset($results[0]);

      // remmove the srchindex and timestamp fields we do not change these
      $results = array_slice($results, 0, -3);

      //loop over remaining table fields
      foreach ($results as $col) {
        // get varchar size
        preg_match_all('!\d+!', $col->Type, $size);
        if (count($size[0]) == 1) {
          $type = explode("(", $col->Type, 2);
          switch ($type[0]) {
              case 'varchar':
                  array_push($schema, [$col->Field => $this->setVarchar((int) $size[0][0])]);
                  break;
              case 'int':
              case 'mediumint':
              case 'bigint':
                  array_push($schema, [$col->Field => $this->setInteger($type[0])]);
                  break;                                      
          }
        }
        else {
          // if size isn't present then field is a text field
          array_push($schema, [$col->Field => $this->setText($col->Type)]);
        }
      }
      
      // return view
      return view('table.edit.schema')->with('tblId', $table->id)
                                      ->with('schema', $schema)
                                      ->with('tblNme', $table->tblNme)
                                      ->with('collctnId', $table->collection_id);
    }      

    public function updateSchema(Request $request) {
      //get table
      $table = Table::where('tblNme', $request->tblNme)->first();

      // Get Description of table
      $results = DB::select("DESCRIBE ".$request->tblNme);

      // remove first item we do not change the id of the table
      unset($results[0]);

      // remmove the srchindex and timestamp fields we do not change these
      $results = array_slice($results, 0, -3);

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
    * Show the wizard page
    */
    public function wizard() {
      // Get the collection names
      $collcntNms = Collection::all();

      // Get the list of files in the directory
      $fltFleList = Storage::allFiles($this->strDir);

      // Format the file names by truncating the dir
      foreach ($fltFleList as $key => $value) {
        // check if directory string exists in the path
        if (str_contains($value, $this->strDir.'/')) {
          // replace the string
          $fltFleList[ $key ] = str_replace($this->strDir.'/', '', $value);
        }
      }

      // Compact everything into array
      $wizrdData = array(
        'collcntNms' => $collcntNms,
        'fltFleList' => $fltFleList
      );

      // Check for the countflatfiles
      if ($collcntNms->where('isEnabled', '1')->count()>0) {
        // return the wizard page by passing the collections
        return view('admin/wizard')->with($wizrdData);
      }

      // return the wizard page by passing the collections and list of files
      return view('admin/collection')->with('collcntNms', $collcntNms)->withErrors([ 'Please create active collection here first' ]);
    }

    /**
    * Importing the records into table follows:
    * 1. Input the table name in the meta Directory
    * 2. Store the file on to Storage Directory if uploaded
    * 3. Show the users schema for further verification
    */
    public function import(Request $request) {

      // 1. Input the table name in the meta Directory
      //Rules for validation
      $rules = array(
        'imprtTblNme' => 'required|unique:tables,tblNme|max:30|min:6|alpha_num',
        'colID' => 'required|Integer',
        'fltFile' => 'required|file|mimetypes:text/plain|mimes:txt,dat,csv,tab',
      );

      //Customize the error messages
      $messages = array(
        'imprtTblNme.required' => 'Please enter a table name',
        'imprtTblNme.unique' => 'The table name has already been taken by current or disabled table',
        'imprtTblNme.max' => 'The table name cannot exceed 30 characters',
        'imprtTblNme.min' => 'The table name should be 6 characters or more',
        'imprtTblNme.alpha_num' => 'The table name can only have alphabets or numbers without spaces',
        'colID.required' => 'Please select a collection',
        'colID.Integer' => 'Please select an existing collection',
        'fltFile.required' => 'Please select a valid flat file',
        'fltFile.file' => 'Please select a valid flat file',
        'fltFile.mimetypes' => 'The flat file must be a file of type: text/plain.',
        'fltFile.mimes' => 'The flat file must have an extension: txt, dat, csv, tab.',
      );

      // Validate the request before storing the data
      $this->validate($request, $rules, $messages);

      // Validate the file before upload
      // Get the file
      $thisFltFile = $request->fltFile;

      // Get the file name
      $thisFltFileNme = $thisFltFile->getClientOriginalName();

      // check if the file exists
      // Get the list of files in the directory
      $fltFleList = Storage::allFiles($this->strDir);

      // check the file name in the file list array
      if (in_array($this->strDir.'/'.$thisFltFileNme, $fltFleList)) {
        return redirect()->route('tableIndex')->withErrors([ 'File already exists. Please select the file or rename and re-upload.' ]);
      }

      // 2. Store the file on to Storage Directory if uploaded
      // Store in the directory inside storage/app
      $thisFltFile->storeAs($this->strDir, $thisFltFileNme);

      // 3. Show the users schema for further verification
      $schema = (new CSVHelper)->schema($this->strDir.'/'.$thisFltFileNme);

      // If the file isn't valid return with an error
      if (!$schema) {
        Storage::delete($fltFleAbsPth);
        return redirect()->route('tableIndex')->withErrors([ 'The selected flat file must be of type: text/plain', 'The selected flat file should not be empty', 'File is deleted for security reasons' ]);
      }

      // Return the view with filename and schema
      return view('admin.schema')->with('schema', $schema)
                                 ->with('tblNme', $request->imprtTblNme)
                                 ->with('fltFile', $thisFltFileNme)
                                 ->with('collctnId', $request->colID);
    }

    public function importCMSDIS(Request $request) {
        $data = [
            'strDir' => $this->strDir,
            'colID' => $request->colID,
            'flatFiles' => $request->cmsdisFiles,
            'cms' => true,
            'tableName' => $request->imprtTblNme
        ];

        return (new TableHelper)->storeUploadsAndImport($data);
    }

    /**
    * Use the existing file to import the records:
    * Algorithm:
    * 1. Get the file name and validate file, if not validated remove it
    * 2. Create the table with schema
    * 3. Show the users with the schema
    */
    public function select(Request $request) {

      // 1. Get the file name and validate file, if not validated remove it
      //Rules for validation
      $rules = array(
        'slctTblNme' => 'required|unique:tables,tblNme|max:30|min:6|alpha_num',
        'colID2' => 'required|Integer',
        'fltFile2' => 'required|string',
      );

      //Customize the error messages
      $messages = array(
        'slctTblNme.required' => 'Please enter a table name',
        'slctTblNme.unique' => 'The table name has already been taken by current or disabled table',
        'slctTblNme.max' => 'The table name cannot exceed 30 characters',
        'slctTblNme.min' => 'The table name should be 6 characters or more',
        'slctTblNme.alpha_num' => 'The table name can only have alphabets or numbers without spaces',
        'colID2.required' => 'Please select a collection',
        'colID2.Integer' => 'Please select an existing collection',
        'fltFile2.required' => 'Please select a valid flat file',
        'fltFile2.string' => 'Please select a valid flat file',
      );

      // Validate the request before storing the data
      $this->validate($request, $rules, $messages);

      // Get the absolute file path
      $thsFltFile = $request->fltFile2;
      $fltFleAbsPth = $this->strDir.'/'.$thsFltFile;
      // validate the file
      if (!Storage::has($fltFleAbsPth)) {
        // if the file doesn't exist
        return redirect()->route('tableIndex')->withErrors([ 'The selected flat file does not exist' ]);
      }

      // 2. Check for file validity and Create the table with schema
      $schema = (new CSVHelper)->schema($fltFleAbsPth);
      // If the file isn't valid return with an error
      if (!$schema) {
        Storage::delete($fltFleAbsPth);
        return redirect()->route('tableIndex')->withErrors([ 'The selected flat file must be of type: text/plain', 'The selected flat file should not be empty', 'File is deleted for security reasons' ]);
      }

      // 3. Show the users with the schema
      return view('admin.schema')->with('schema', $schema)
                                 ->with('tblNme', $request->slctTblNme)
                                 ->with('fltFile', $request->fltFile2)
                                 ->with('collctnId', $request->colID2);
    }

    public function selectCMSDIS(Request $request) {
        $data = [
            'strDir' => $this->strDir,
            'colID' => $request->colID2,
            'flatFiles' => $request->cmsdisFiles2,
            'cms' => true,
            'tableName' => $request->slctTblNme
        ];

        return (new TableHelper)->selectFilesAndImport($data); 
    }

    /**
    * Method to read input from the schema and start actual data import
    */
    public function finalize(Request $request) {
      // 1. Get the number of columns, collctn id and table name before creating the table
      $kVal = intval($request->kCnt);
      $tblNme = strval($request->tblNme);
      $fltFile = strval($request->fltFile);
      $collctnId = strval($request->collctnId);

      // Before anything validate that all the data is strings
      // Define array
      $rules = array();
      // Add rule for each entry in request
      for ($i = 0; $i<$kVal; $i++) {
        // Rules for all names
        $curNme = 'col-'.$i.'-name';
        $rules[ $curNme ] = 'required|alpha_dash';
        // Rules for all data type
        // $curDTyp = 'col-'.$i.'-data';
        $rules[ $curNme ] = 'required|string';
        // Rules for all data sizes
        $curDataSz = 'col-'.$i.'-size';
        $rules[ $curDataSz ] = 'required|string';
      }
      // Validate
      $this->validate($request, $rules);

      // 2. Create the table
      Schema::connection('mysql')->create($tblNme, function(Blueprint $table) use($kVal, $request){
        // Default primary key
        $table->increments('id');
        // Add all the dynamic columns
        for ($i = 0; $i<$kVal; $i++) {
          // Define current column name, type and size
          $curColNme = strval($request->{'col-'.$i.'-name'});
          $curColType = strval($request->{'col-'.$i.'-data'});
          $curColSze = strval($request->{'col-'.$i.'-size'});

          (new TableHelper)->setupTableField($table, $curColNme, $curColType, $curColSze);
        }

        // search index
        $table->longText('srchindex');

        // Time stamps
        $table->timestamps();
      });

      // modify table for fulltext search using the srchindex column
      DB::connection()->getPdo()->exec('ALTER TABLE '.$tblNme.' ADD FULLTEXT fulltext_index (srchindex)');

      // Finally create the table
      // Save the table upon the schema
      (new TableHelper)->crteTblInCollctn($tblNme, $collctnId);

      // Finally return the view to load data
      return $this->load(True, $tblNme, $fltFile);
    }

    /**
    * Method to format the storage files
    **/
    public function getFiles($strDir) {
      // Get the list of files in the directory
      $fltFleList = Storage::allFiles($strDir);

      // Format the file names by truncating the dir
      foreach ($fltFleList as $key => $value) {
        // check if directory string exists in the path
        if (str_contains($value, $this->strDir.'/')) {
          // replace the string
          $fltFleList[ $key ] = str_replace($this->strDir.'/', '', $value);
        }
      }

      // return the list
      return $fltFleList;
    }

    /**
    * Method responsible to load the data into the given table
    **/
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
    **/
    public function store(Request $request) {
      // validate the file name and table name
      // Rules for validation
      $rules = array(
        'fltFle' => 'required|string',
        'tblNme' => 'required|string'
      );

      // Validate the request before storing the job
      $this->validate($request, $rules);

      //Queue Job for Import
      (new TableHelper)->fileImport($request->tblNme, $this->strDir, $request->fltFle);

      return redirect()->route('tableIndex');
    }

    /**
     * takes a string and prepares it to be used inserted as a new record
     * if we do not find enough items in the line we will check and see if
     * the previous line was saved and merge them and check the count again.
     * if their is insufficent items we will save the tkns and the row position
     * so we can attempt a merge later.
     * @param string $curLine current line read from the file
     * @param string $delimiter type of delmiter that is used in the file
     * @param integer $orgCount current number of fields expected
     * @param integer $prcssd current position in the file
     * @return string
     */
    public function prepareLine($curLine, $delimiter, $orgCount, $prcssd) {
        // Strip out Quotes that are sometimes seen in csv files around each item
        $curLine = str_replace('"', "", $curLine);

        // Tokenize the line
        $tkns = (new CSVHelper)->tknze($curLine, $delimiter);

        // Validate the tokens and filter them
        $tkns = (new CSVHelper)->fltrTkns($tkns);

        // if lastErrRow is the previous row try to combine the lines
        if ((count($tkns) != $orgCount) && ($this->lastErrRow == $prcssd - 1) && ($this->savedTkns != NULL)) {
            $tkns = (new customStringHelper)->mergeLines($this->savedTkns, $tkns);

            // clear last saved line since we did a merge
            $this->lastErrRow = NULL;
            $this->savedTkns = NULL;
        }

        // if token count doesn't match what is exptected save the tkns and last row position
        if (count($tkns) != $orgCount) {
          // save the last row position and the Tokenized row
          $this->lastErrRow = $prcssd;
          $this->savedTkns = $tkns;
          return (null);
        }

        return ($tkns);
    }

    /**
    * takes array of tokens. Creates a search index and 
    * inserts them into the table
    * 
    * @param array $tkns array containing all fields of record
    * @param integer $orgCount number of expected fields in the record
    * @param object $table table where new record is to be inserted
    * @param object $clmnLst array containing all field names for table
    * @return boolean
    */   
    public function processLine($tkns, $orgCount, $table, $clmnLst) {
        if(!is_array($tkns) && empty($tkns)) { return false; }

        // verify that passed $tkns match the expected field count
        if (count($tkns) == $orgCount) {
          // Declae an array
          $curArry = array();

          // Compact them into one array with utf8 encoding
          for ($i = 0; $i<$orgCount; $i++) {
            $curArry[ strval($clmnLst[ $i ]) ] = utf8_encode($tkns[ $i ]);
          }

          // add srchindex
          $curArry[ 'srchindex' ] = (new customStringHelper)->createSrchIndex(implode(" ", $tkns));

          //insert Record into database
          $table->insertRecord($curArry);

          return true;
        }
        return false;
    }

    /**
     * Process employs following algorithm:
     * get all the column names from table name
     * 1. Read the file as spl object
     * 2. For each line
     *   1. Validate
     * @param string $tblNme
     * @param string $fltFlePath
     * @param string $fltFleNmetype
     * @param boolean $ignoreFirst 
     * @return void
     */
    public function process($tblNme, $fltFlePath, $fltFleNme, $ignoreFirst = true) {
      //get table
      $table = Table::where('tblNme', $tblNme)->first();

      $clmnLst = $table->getColumnList();

      // remove the id and time stamps
      $clmnLst = array_splice($clmnLst, 1, count($clmnLst) - 3);

      // determine number of fields without the srchIndex
      $orgCount = count($clmnLst) - 1;

      // 1. Read the file as spl object
      $fltFleAbsPth = $fltFlePath.'/'.$fltFleNme;
      $fltFleFullPth = storage_path('app/'.$fltFleAbsPth);

      // Create an instance for the file
      $curFltFleObj = new \SplFileObject($fltFleFullPth);

      // Detect delimiter used in file
      $delimiter = (new CSVHelper)->detectDelimiter($fltFleFullPth);
      
      //Check for an empty file
      if (filesize($fltFleFullPth)>0) {

        // Ignore the first line unless set to false
        if ($ignoreFirst) { $curFltFleObj->seek(1); }

        // Counter for processed
        $prcssd = 0;

        // For each line
        while ($curFltFleObj->valid()) {
          // Get the line
          $curLine = $curFltFleObj->current();

          $tkns = $this->prepareLine($curLine, $delimiter, $orgCount, $prcssd);

          $added = $this->processLine($tkns, $orgCount, $table, $clmnLst);

          if ($added) {
            // Update the counter
            $prcssd += 1;
          }

          $curFltFleObj->next();
        }
      }
      else {
        throw new \Exception("Cannot Import a Empty File.");
      }
    }

    /**
    * Disable the given table
    */
    public function restrict(Request $request) {
      // Create the collection name
      $thisTbl = Table::findOrFail($request->id);
      $thisTbl->hasAccess = false;
      $thisTbl->save();
      return redirect()->route('tableIndex');
    }

}
