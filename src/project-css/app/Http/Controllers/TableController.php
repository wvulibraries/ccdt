<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Import the storage class too
use Illuminate\Support\Facades\Storage;

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
  * 2. Create the table record with schema
  * 3. Store the file on to Storage Directory if uploaded
  * 4. Show the users schema for further verification
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

    // 3. Store the file on to Storage Directory if uploaded
    // Store in the directory inside storage/app
    $thisFltFile->storeAs($this->strDir,$thisFltFileNme);


    // 4. Show the users schema for further verification
    $schema = $this->schema($this->strDir.'/'.$thisFltFileNme);
    // If the file isn't valid return with an error
    if(!$schema){
      Storage::delete($fltFleAbsPth);
      return redirect()->route('tableIndex')->withErrors(['The selected flat file must be of type: text/plain','The selected flat file should not be empty','File is deleted for security reasons']);
    }

    // 2. Create the table with schema
    $thisTabl = new Table;
    $thisTabl->tblNme = $request->imprtTblNme;
    $thisTabl->collection_id = $request->colID;
    $thisTabl->save();

    // Return the view with filename and schema
    return view('admin.schema')->with('schema',$schema)->with('tblNme',$request->imprtTblNme);
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

    // Save the table upon the schema
    $thisTabl = new Table;
    $thisTabl->tblNme = $request->slctTblNme;
    $thisTabl->collection_id = $request->colID;
    $thisTabl->save();

    // 3. Show the users with the schema
    return view('admin.schema')->with('schema',$schema);
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
    $tkns = preg_split($pattern,trim($line));

    // Return the array
    return $tkns;
  }

  /**
  * Get the line numbers for a fileobject
  */
  public function isEmpty($fltFleObj){
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
      if(empty(trim($tkn))){
        // Replace the content with null
        $tkns[$key] = "null";
      }
    }

    // Return the filtered tokens
    return $tkns;
  }

  /**
  * Method to read input from the schema and start actual data import
  */
  public function finalize($request){

  }

}
