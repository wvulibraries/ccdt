<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
    // return the index page
    return view('admin/table');
  }

  /**
  * Show the wizard page
  */
  public function wizard(){
    // return the wizard page
    return view('admin/wizard');
  }

  /**
  * Importing the records into table follows:
  * 1. Input the table name in the meta Directory
  * 2. Create the table with schema
  * 3. Store the file on to Storage Directory if uploaded
  * 4. Show the users schema for further verification
  */
  public function import(Request $request){
    // 1. Input the table name in the meta Directory
    //Rules for validation
    $rules = array(
      'tblNme' => 'required|unique:tables|max:30|min:6|alpha_num',
    );

    //Customize the error messages
    $messages = array(
      'tblNme.required' => 'Please enter a table name',
      'tblNme.unique' => 'The table name has already been taken by current or disabled table',
      'tblNme.max' => 'The table name cannot exceed 30 characters',
      'tblNme.min' => 'The table name should be 6 characters or more',
      'tblNme.alpha_num' => 'The table name can only have alphab',
    );

    // Validate the request before storing the data
    $this->validate($request,$rules,$messages);

    // 4. Show the users schema for further verification
    return back();

  }
}
