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
use App\Libraries\CustomStringHelper;

class TableController extends Controller
{
    private $strDir;
    private $extraClmns;
    public $lastErrRow;
    public $savedTkns;

    /**
     * Create a new controller instance.
     */
    public function __construct() {
      // Protection to make sure this is only accessible to admin
      $this->middleware('admin');
      // Storage directory
      $this->strDir = 'flatfiles';
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
        'fltFile' => 'required|file|mimetypes:text/plain|mimes:txt,dat,csv',
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
        'fltFile.mimes' => 'The flat file must have an extension: txt, dat, csv.',
      );

      // Validate the request before storing the data
      $this->validate($request, $rules, $messages);

      // Validate the file before upload
      // Get the file
      $thisFltFile = $request->fltFile;
      // Get the file name
      $thisFltFileNme = $thisFltFile->getClientOriginalName();
      // Get the client extension
      // $thisFltFileExt = $thisFltFile->getClientOriginalExtension();
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
      $schema = $this->schema($this->strDir.'/'.$thisFltFileNme);
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
      $schema = $this->schema($fltFleAbsPth);
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

    /**
    * Simple function to create the table within the collections
    * @param string $tblNme
    * @param string $collctnId
    */
    public function crteTblInCollctn($tblNme, $collctnId) {
      // declare a new table instance
      $thisTabl = new Table;
      // Assign the table name and collctn id
      $thisTabl->tblNme = $tblNme;
      $thisTabl->collection_id = $collctnId;
      // Save the collection
      $thisTabl->save();
    }

    /**
    * Method to validate the file type and read the first line only for schema
    * Algorithm:
    * 1. Get the flatfile instance
    * 2. Validate the file type
    * 3. Tokenize the current line
    * 4. Validate the tokens and return
    * 5. Return first line as array
    * @param string $fltFlePth
    * @return boolean
    */
    public function schema($fltFlePth) {
      // 1. Get the flatfile instance
      // Check if the file exists
      if (!Storage::has($fltFlePth)) {
        // If the file doesn't exists return with error
        return false;
      }
      // Create an instance for the file
      $fltFleObj = new \SplFileObject(\storage_path()."/app/".$fltFlePth);

      // 2. Validate the file type
      // Create a finfo instance
      $fleInf = new \finfo(FILEINFO_MIME_TYPE);
      // Get the file type
      $fleMime = $fleInf->file($fltFleObj->getRealPath());
      // Check the mimetype
      if (!str_is($fleMime, "text/plain")) {
        // If the file isn't a text file return false
        return false;
      }
      // Check if the file is empty
      if (!$this->isEmpty($fltFleObj)>0) {
        return false;
      }

      // 3. Tokenize the current line
      // Get the first line as the header
      $fltFleObj->seek(0);
      $hdr = $fltFleObj->fgets();

      // Strip out Quotes that are sometimes seen in header rows of csv files
      $hdr = str_replace('"', "", $hdr);

      // Tokenize the line
      $tkns = $this->tknze($hdr, $this->detectDelimiter(\storage_path()."/app/".$fltFlePth));
      // Validate the tokens and filter them
      $tkns = $this->fltrTkns($tkns);

      // Returning tokens
      return $tkns;
    }

    /**
    * Method to tokenize the string for multiple lines
    * @param string $line
    * @param false|string $delimiter
    */
    public function tknze($line, $delimiter) {
      // Tokenize the line
      // Define a pattern
      $pattern = '/['.$delimiter.']/';

      // preg split
      $tkns = preg_split($pattern, $line);

      // Return the array
      return $tkns;
    }

     /*
     * @param string $csvFile Path to the CSV file
     * @return string Delimiter
     */
     public function detectDelimiter($csvFile)
     {
         $delimiters = array(
             ';' => 0,
             ',' => 0,
             "\t" => 0,
             "|" => 0
         );

         $handle = fopen($csvFile, "r");
         $firstLine = fgets($handle);
         fclose($handle);
         foreach ($delimiters as $delimiter => &$count) {
             $count = count(str_getcsv($firstLine, $delimiter));
         }

         return array_search(max($delimiters), $delimiters);
     }

    /**
    * Get the line numbers for a fileobject
    * @param \SplFileObject $fltFleObj
    */
    public function isEmpty($fltFleObj) {
      // Before anytthing set to seek first line
      $fltFleObj->seek(0);

      // Variable to count the length
      $len = 0;

      // Loop till EOF
      while (!$fltFleObj->eof()) {
        // Check if the file has at least one line
        if ($len>=1) {
          // Break here so that it's not reading huge files
          break;
        }
        // Increament variable
        $len += 1;
        // Seek the next item
        $fltFleObj->next();
      }

      // Return the length
      return $len;
    }

    /**
    * Method to check if the given tkns are null
    */
    public function fltrTkns($tkns) {
      // Run through the files
      foreach ($tkns as $key => $tkn) {
        // trim the token
        $tkns[ $key ] = trim($tkn);
      }

      // Return the filtered tokens
      return $tkns;
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

          // Filter the data type and size and create the column
          // Check for Strings
          if (str_is($curColType, 'string')) {
            // Check for the data type
            // Default
            if (str_is($curColSze, 'default')) {
              // For String default is 30 characters
              $table->string($curColNme, 50)->default("Null");
            }
            // Medium
            if (str_is($curColSze, 'medium')) {
              // For String medium is 150 characters
              $table->string($curColNme, 150)->default("Null");
            }
            // Big
            if (str_is($curColSze, 'big')) {
              // For String big is 500 characters
              $table->string($curColNme, 500)->default("Null");
            }
          }

          // Check for Text data type
          if (str_is($curColType, 'text')) {
            // Check for the data type
            // Default
            if (str_is($curColSze, 'default')) {
              // For text default is text type
              $table->text($curColNme);
            }
            // Medium
            if (str_is($curColSze, 'medium')) {
              // For text medium is mediumtext type
              $table->mediumText($curColNme);
            }
            // Big
            if (str_is($curColSze, 'big')) {
              // For text big is longtext type
              $table->longText($curColNme);
            }
          }

          // Check for Integer
          if (str_is($curColType, 'integer')) {
            // Check for the data type
            // Default
            if (str_is($curColSze, 'default')) {
              // For Integer default integer type
              $table->integer($curColNme)->default(0);
            }
            // Medium
            if (str_is($curColSze, 'medium')) {
              // For Integer medium is medium integer
              $table->mediumInteger($curColNme)->default(0);
            }
            // Big
            if (str_is($curColSze, 'big')) {
              // For Integer big is big integer
              $table->bigInteger($curColNme)->default(0);
            }
          }
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
      $this->crteTblInCollctn($tblNme, $collctnId);

      // create folder in storage that will contain any additional files associated to the table
      if (Storage::exists($tblNme) == FALSE) {
        Storage::makeDirectory($tblNme);
      }

      // Finally return the view to load data
      return $this->load(True, $tblNme, $fltFile);
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

      // set messages array to empty
      $messages = [ ];

      Log::info('File Import has been requested for table '.$request->tblNme.' using flat file '.$request->fltFle);
      // add job to queue
      $this->dispatch(new FileImport($request->tblNme, $request->fltFle));
      $message = [
        'content'  =>  $request->fltFle.' has been queued for import to '.$request->tblNme.' table. It will be available shortly.',
        'level'    =>  'success',
      ];
      array_push($messages, $message);
      session()->flash('messages', $messages);
      return redirect()->route('tableIndex');
    }

    // if 2 lines are read that do not contain enough fields we will attempt to
    // merge them to get the required number of Fields we assume that the last array
    // item in $tkns1 is continued in the first item of $tkns2 so they will be
    // combined.
    public function mergeLines($tkns1, $tkns2) {
        $numItem = count($tkns1) - 1;
        $tkns1[ $numItem ] = $tkns1[ $numItem ] . ' ' . $tkns2[ 0 ];
        unset($tkns2[ 0 ]);
        return( (count($tkns2) > 0) ? array_merge($tkns1, $tkns2) : $tkns1 );
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
        $tkns = $this->tknze($curLine, $delimiter);

        // Validate the tokens and filter them
        $tkns = $this->fltrTkns($tkns);

        // if lastErrRow is the previous row try to combine the lines
        if ((count($tkns) != $orgCount) && ($this->lastErrRow == $prcssd - 1) && ($this->savedTkns != NULL)) {
            $tkns = $this->mergeLines($this->savedTkns, $tkns);

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
     * takes a string and prepares it to be used as a search index for fulltext search
     * @param string $curLine
     * @return string
     */
    public function createSrchIndex($curLine) {
      // remove extra characters replacing them with spaces
      // also remove .. that is in the filenames
      $cleanString = preg_replace('/[^A-Za-z0-9._ ]/', ' ', str_replace('..', '', $curLine));

      // remove extra spaces and make string all lower case
      $cleanString = strtolower(preg_replace('/\s+/', ' ', $cleanString));

      // remove duplicate keywords in the srchindex
      $srchArr = explode(' ', $cleanString);

      // remove any items less than 2 characters
      // as fulltext searches need at least 2 characters
      $counter = 0;
      foreach ($srchArr as $value) {
        if (strlen($value)<2) {
          unset($srchArr[ $counter ]);
        }
       $counter++;
      }

      // remove duplicate keywords from the srchIndex
      $srchArr = array_unique($srchArr);
      return(implode(' ', (new customStringHelper)->removeCommonWords($srchArr)));
    }

    /**
    * Process employs following algorithm:
    * get all the column names from table name
    * 1. Read the file as spl object
    * 2. For each line
    *   1. Validate
    *   2. Insert into database
    **/
    public function process($tblNme, $fltFleNme) {
      //get table
      $table = Table::where('tblNme', $tblNme)->first();
      $clmnLst = $table->getColumnList();

      // remove the id and time stamps
      $clmnLst = array_splice($clmnLst, 1, count($clmnLst) - 3);

      // determine number of fields without the srchIndex
      $orgCount = count($clmnLst) - 1;

      // 1. Read the file as spl object
      $fltFleAbsPth = $this->strDir.'/'.$fltFleNme;
      $fltFleFullPth = storage_path('app/'.$fltFleAbsPth);

      // Create an instance for the file
      $curFltFleObj = new \SplFileObject($fltFleFullPth);

      $delimiter = $this->detectDelimiter($fltFleFullPth);

      //Check for an empty file
      if (filesize($fltFleFullPth)>0) {

        // Ignore the first line
        $curFltFleObj->seek(1);

        // Counter for processed
        $prcssd = 0;

        // For each line
        while ($curFltFleObj->valid()) {
          // Get the line
          $curLine = $curFltFleObj->current();

          $tkns = $this->prepareLine($curLine, $delimiter, $orgCount, $prcssd);

          // verify that passed $tkns match the expected field count
          if (count($tkns) == $orgCount) {
            // Declae an array
            $curArry = array();

            // Compact them into one array with utf8 encoding
            for ($i = 0; $i<$orgCount; $i++) {
              $curArry[ strval($clmnLst[ $i ]) ] = utf8_encode($tkns[ $i ]);
            }

            // add srchindex
            $curArry[ 'srchindex' ] = $this->createSrchIndex(implode(" ", $tkns));

            //insert Record into database
            $table->insertRecord($curArry);
          } else {
            throw new \Exception("Invalid Field Count - detected ".count($tkns)." expected ".$orgCount);
          }

          // Update the counter
          $prcssd += 1;
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
