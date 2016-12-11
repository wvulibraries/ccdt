<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Import the table and collection models
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
      $this->middleware('admin');
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

    // Check for the count
    if($collcntNms->where('isEnabled','1')->count()>0){
      // return the wizard page by passing the collections
      return view('admin/wizard')->with('collcntNms',$collcntNms);
    }

    // return the wizard page by passing the collections
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
    //Get the file
    $thisFltFile = $request->fltFile;
    //Store in the directory inside storage/app
    $thisFltFile->store('flatfiles');

    // 4. Show the users schema for further verification
    return redirect()->route('tableIndex');

  }
}
