<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Import the storage class too
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;

// Import the supported facades for creating tables
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

// Import the table and collection models
use App\Table;
use App\Collection;

class TableController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    // Protection to make sure this only accessible to admin
    $this->middleware('admin');
    // Storage directory
    $this->strDir = 'flatfiles';
    // offset for extra colmns
    $this->extraClmns = 3;
  }

  /**
  * Show the table index page
  */
  public function index(){
    // Get all the table records
    $tbls = Table::all();
    // return the index page
    return view('admin/table')->with('tbls',$tbls);
  }

  /**
  * Show the wizard page
  */
  public function wizard(){
    // Get the collection names
    $collcntNms = Collection::all();

    // Get the list of files in the directory
    $fltFleList = Storage::allFiles($this->strDir);

    // Format the file names by truncating the dir
    foreach ($fltFleList as $key => $value) {
      // check if directory string exists in the path
      if(str_contains($value,$this->strDir.'/')){
        // replace the string
        $fltFleList[$key] = str_replace($this->strDir.'/','',$value);
      }
    }

    // Compact everything into array
    $wizrdData = array(
      'collcntNms' => $collcntNms,
      'fltFleList' => $fltFleList
    );

    // Check for the countflatfiles
    if($collcntNms->where('isEnabled','1')->count()>0){
      // return the wizard page by passing the collections
      return view('admin/wizard')->with($wizrdData);
    }

    // return the wizard page by passing the collections and list of files
    return view('admin/collection')->with('collcntNms',$collcntNms)->withErrors(['Please create active collection here first']);
  }

  /**
  * Importing the records into table follows:
  * 1. Input the table name in the meta Directory
  * 2. Store the file on to Storage Directory if uploaded
  * 3. Show the users schema for further verification
  */
  public function import(Request $request){
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
    $this->validate($request,$rules,$messages);

    // Validate the file before upload
    // Get the file
    $thisFltFile = $request->fltFile;
    // Get the file name
    $thisFltFileNme = $thisFltFile->getClientOriginalName();
    // Get the client extension
    $thisFltFileExt = $thisFltFile->getClientOriginalExtension();
    // check if the file exists
    // Get the list of files in the directory
    $fltFleList = Storage::allFiles($this->strDir);
    // check the file name in the file list array
    if(in_array($this->strDir.'/'.$thisFltFileNme,$fltFleList)){
      return redirect()->route('tableIndex')->withErrors(['File already exists. Please select the file or rename and re-upload.']);
    }

    // 2. Store the file on to Storage Directory if uploaded
    // Store in the directory inside storage/app
    $thisFltFile->storeAs($this->strDir,$thisFltFileNme);


    // 3. Show the users schema for further verification
    $schema = $this->schema($this->strDir.'/'.$thisFltFileNme);
    // If the file isn't valid return with an error
    if(!$schema){
      Storage::delete($fltFleAbsPth);
      return redirect()->route('tableIndex')->withErrors(['The selected flat file must be of type: text/plain','The selected flat file should not be empty','File is deleted for security reasons']);
    }

    // Return the view with filename and schema
    return view('admin.schema')->with('schema',$schema)
                               ->with('tblNme',$request->imprtTblNme)
                               ->with('fltFile',$thisFltFileNme)
                               ->with('collctnId',$request->colID);
  }

  /**
  * Use the existing file to import the records:
  * Algorithm:
  * 1. Get the file name and validate file, if not validated remove it
  * 2. Create the table with schema
  * 3. Show the users with the schema
  */
  public function select(Request $request){
    // 1. Get the file name and validate file, if not validated remove it
    //Rules for validation
    $rules = array(
      'slctTblNme' => 'required|unique:tables,tblNme|max:30|min:6|alpha_num',
      'colID' => 'required|Integer',
      'fltFile' => 'required|string',
    );

    //Customize the error messages
    $messages = array(
      'slctTblNme.required' => 'Please enter a table name',
      'slctTblNme.unique' => 'The table name has already been taken by current or disabled table',
      'slctTblNme.max' => 'The table name cannot exceed 30 characters',
      'slctTblNme.min' => 'The table name should be 6 characters or more',
      'slctTblNme.alpha_num' => 'The table name can only have alphabets or numbers without spaces',
      'colID.required' => 'Please select a collection',
      'colID.Integer' => 'Please select an existing collection',
      'fltFile.required' => 'Please select a valid flat file',
      'fltFile.string' => 'Please select a valid flat file',
    );

    // Validate the request before storing the data
    $this->validate($request,$rules,$messages);

    // Get the absolute file path
    $thsFltFile = $request->fltFile;
    $fltFleAbsPth = $this->strDir.'/'.$thsFltFile;
    // validate the file
    if(!Storage::has($fltFleAbsPth)){
      // if the file doesn't exist
      return redirect()->route('tableIndex')->withErrors(['The selected flat file does not exist']);
    }

    // 2. Check for file validity and Create the table with schema
    $schema = $this->schema($fltFleAbsPth);
    // If the file isn't valid return with an error
    if(!$schema){
      Storage::delete($fltFleAbsPth);
      return redirect()->route('tableIndex')->withErrors(['The selected flat file must be of type: text/plain','The selected flat file should not be empty','File is deleted for security reasons']);
    }

    // 3. Show the users with the schema
    return view('admin.schema')->with('schema',$schema)
                               ->with('tblNme',$request->slctTblNme)
                               ->with('fltFile',$request->fltFile)
                               ->with('collctnId',$request->colID);
  }

  /**
  * Simple function to create the table within the collections
  */
  public function crteTblInCollctn($tblNme,$collctnId){
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
  */
  public function schema($fltFlePth){
    // 1. Get the flatfile instance
    // Check if the file exists
    if(!Storage::has($fltFlePth)){
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
    if(!str_is($fleMime,"text/plain")){
      // If the file isn't a text file return false
      return false;
    }
    // Check if the file is empty
    if(!$this->isEmpty($fltFleObj)>0){
      return false;
    }

    // 3. Tokenize the current line
    // Get the first line as the header
    $fltFleObj->seek(0);
    $hdr = $fltFleObj->fgets();
    // Tokenize the line
    $tkns = $this->tknze($hdr);
    // Validate the tokens and filter them
    $tkns = $this->fltrTkns($tkns);

    // Returning tokens
    return $tkns;
  }

  /**
  * Method to tokenize the string for multiple lines
  */
  public function tknze($line){
    // Tokenize the line
    // Define a pattern
    $pattern = '/[;,\t]/';
    // preg split
    $tkns = preg_split($pattern,$line);

    // Return the array
    return $tkns;
  }

  /**
  * Get the line numbers for a fileobject
  */
  public function isEmpty($fltFleObj){
    // Before anytthing set to seek first line
    $fltFleObj->seek(0);

    // Variable to count the length
    $len = 0;

    // Loop till EOF
    while(!$fltFleObj->eof()){
      // Check if the file has atleast one line
      if($len>=1){
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
  public function fltrTkns($tkns){
    // Run through the files
    foreach($tkns as $key => $tkn){
      // Check if the token is null
      // if(empty(trim($tkn))){
      //   // Replace the content with null
      //   $tkns[$key] = "null";
      // }
      // trim the token
      $tkns[$key]=trim($tkn);
    }

    // Return the filtered tokens
    return $tkns;
  }

  /**
  * Method to read input from the schema and start actual data import
  */
  public function finalize(Request $request){
    // 1. Get the number of columns, collctn id and table name before creating the table
    $kVal = intval($request->kCnt);
    $tblNme = strval($request->tblNme);
    $fltFile = strval($request->fltFile);
    $collctnId = strval($request->collctnId);

    // Before anything validate that all the data is strings
    // Define array
    $rules=array();
    // Add rule for each entry in request
    for($i=0;$i<$kVal;$i++){
      // Rules for all names
      $curNme = 'col-'.$i.'-name';
      $rules[$curNme]='required|alpha_dash';
      // Rules for all data type
      $curDTyp = 'col-'.$i.'-data';
      $rules[$curNme]='required|string';
      // Rules for all data sizes
      $curDataSz = 'col-'.$i.'-size';
      $rules[$curDataSz]='required|string';
    }
    // Validate
    $this->validate($request,$rules);

    // 2. Create the table
    Schema::connection('mysql')->create($tblNme,function(Blueprint $table) use($kVal,$request){
      // Default primary key
      $table->increments('id');
      // Add all the dynamic columns
      for($i=0;$i<$kVal;$i++){
        // Define current column name, type and size
        $curColNme = strval($request->{'col-'.$i.'-name'});
        $curColType = strval($request->{'col-'.$i.'-data'});
        $curColSze = strval($request->{'col-'.$i.'-size'});

        // Filter the data type and size and create the column
        // Check for Strings
        if(str_is($curColType,'string')){
          // Check for the data type
          // Default
          if(str_is($curColSze,'default')){
            // For String default is 30 characters
            $table->string($curColNme,50)->default("Null");
          }
          // Medium
          if(str_is($curColSze,'medium')){
            // For String medium is 150 characters
            $table->string($curColNme,150)->default("Null");
          }
          // Big
          if(str_is($curColSze,'big')){
            // For String big is 500 characters
            $table->string($curColNme,500)->default("Null");
          }
        }

        // Check for Text data type
        if(str_is($curColType,'text')){
          // Check for the data type
          // Default
          if(str_is($curColSze,'default')){
            // For text default is text type
            $table->text($curColNme);
          }
          // Medium
          if(str_is($curColSze,'medium')){
            // For text medium is mediumtext type
            $table->mediumText($curColNme);
          }
          // Big
          if(str_is($curColSze,'big')){
            // For text big is longtext type
            $table->longText($curColNme);
          }
        }

        // Check for Integer
        if(str_is($curColType,'integer')){
          // Check for the data type
          // Default
          if(str_is($curColSze,'default')){
            // For Integer default integer type
            $table->integer($curColNme)->default(0);
          }
          // Medium
          if(str_is($curColSze,'medium')){
            // For Integer medium is medium integer
            $table->mediumInteger($curColNme)->default(0);
          }
          // Big
          if(str_is($curColSze,'big')){
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
    DB::connection()->getPdo()->exec( 'ALTER TABLE ' . $tblNme . ' ADD FULLTEXT fulltext_index (srchindex)');

    // Check for the number of columns we actually added into database

    // Finally create the table
    // Save the table upon the schema
    $this->crteTblInCollctn($tblNme,$collctnId);

    // Finally return the view to load data
    return $this->load(True,$tblNme,$fltFile);
  }

  /**
  * Method responsible to load the data into the given table
  **/
  public function load($isFrwded=False,$tblNme="",$fltFle=""){
    // Check if the request is formwarded
    if($isFrwded){
      // Forward the file and table name
      $tblNms=Table::where('tblNme', $tblNme)->get();
      $fltFleList=array($fltFle);
    }
    else{
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
  public function getFiles($strDir){
    // Get the list of files in the directory
    $fltFleList = Storage::allFiles($strDir);

    // Format the file names by truncating the dir
    foreach ($fltFleList as $key => $value) {
      // check if directory string exists in the path
      if(str_contains($value,$this->strDir.'/')){
        // replace the string
        $fltFleList[$key] = str_replace($this->strDir.'/','',$value);
      }
    }

    // return the list
    return $fltFleList;
  }

  /**
  * Simple method to get the column listing
  **/
  public function getColLst($tblNme){
    // Returns the column names as an array
    return Schema::getColumnListing($tblNme);
  }

  /**
  * Worker employs following algorithm:
  * validate the file name and table name
  * get all the column names from table name
  * 1. Read the file as spl object
  * 2. For each line
  *   1. Validate
  *   2. Insert into database
  **/
  public function worker(Request $request){
    // validate the file name and table name
    //Rules for validation
    $rules = array(
      'fltFle' => 'required|string',
      'tblNme' => 'required|string'
    );

    // Validate the request before storing the data
    $this->validate($request,$rules);

    // get all column names
    $clmnLst = $this->getColLst($request->tblNme);

    // remove the id and time stamps
    $clmnLst = array_splice($clmnLst,1,count($clmnLst)-3);

    // 1. Read the file as spl object
    $fltFleNme = $request->fltFle;
    $fltFleAbsPth = $this->strDir.'/'.$fltFleNme;

    // Create an instance for the file
    $curFltFleObj = new \SplFileObject(\storage_path()."/app/".$fltFleAbsPth);

    //Check for an empty file
    if($this->isEmpty($curFltFleObj)>0){
      // Ignore the first line
      $curFltFleObj->seek(1);

      // Counter for processed
      $prcssd = 0;

      // For each line
      while($curFltFleObj->valid()){
        // Get the line
        $curLine = $curFltFleObj->current();

        // Tokenize the line
        $tkns = $this->tknze($curLine);

        // Validate the tokens and filter them
        $tkns = $this->fltrTkns($tkns);

        // Size of both column array and data should be same
        // Count of tokens
        $orgCount = count($clmnLst)-1;

        if(count($tkns)==$orgCount){
          // Declae an array
          $curArry = array();

          // Compact them into one array with utf8 encoding
          for($i=0;$i<$orgCount;$i++){
            // added iconv to strip out invalid characters
            $curArry[strval($clmnLst[$i])]=utf8_encode($tkns[$i]);
          }

          // add srchindex
          $curArry["srchindex"]=utf8_encode(implode(" ", $tkns));

          // Insert them into DB
          \DB::table($request->tblNme)->insert($curArry);

          // to do if creating specific column to search
          // sanitize $curLine removing all special chars and ,
          // that isn't used strip out extra spaces
          // store result in search.index column
        }

        // Update the counter
        $prcssd+=1;
        $curFltFleObj->next();
      }
    }

    return redirect()->route('tableIndex');
  }

  /**
  * Disable the given table
  */
  public function restrict(Request $request){
    // Create the collection name
    $thisTbl = Table::findorFail($request->id);
    $thisTbl->hasAccess = false;
    $thisTbl->save();
    return redirect()->route('tableIndex');
  }

}
