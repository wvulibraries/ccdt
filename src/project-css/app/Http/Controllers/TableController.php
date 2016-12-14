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

    // 2. Create the table with schema
    $thisTabl = new Table;
    $thisTabl->tblNme = $request->imprtTblNme;
    $thisTabl->collection_id = $request->colID;
    $thisTabl->save();

    // 3. Store the file on to Storage Directory if uploaded
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
    // Store in the directory inside storage/app
    $thisFltFile->storeAs($this->strDir,$thisFltFileNme);


    // 4. Show the users schema for further verification
    $schema = $this->schema($this->strDir.'/'.$thisFltFileNme);
    return view('table/schema')->with('schema',$schema);
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
    // validate the file
    if(!Storage::has($this->strDir.'/'.$thsFltFile)){
      // if the file doesn't exist
      return redirect()->route('tableIndex')->withErrors(['The selected flat file does not exist']);
    }

    // 2. Create the table with schema
    $thisTabl = new Table;
    $thisTabl->tblNme = $request->slctTblNme;
    $thisTabl->collection_id = $request->colID;
    $thisTabl->save();

    // 3. Show the users with the schema
    $schema = $this->schema($this->strDir.'/'.$thsFltFile);
    return view('table/schema')->with('schema',$schema);
  }

  /**
  * Method to validate the file type and read the first line
  */
  public function schema($fltFleNme){
    return $fltFleNme;
  }
}
