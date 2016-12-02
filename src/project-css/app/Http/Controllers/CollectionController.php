<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Import the Collection model
use App\Collection;

class CollectionController extends Controller
{
  /**
   * Where to redirect users after Creating / selecting collections.
   *
   * @var string
   */
  protected $redirectTo = '/home';

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
  * Show the collection index page
  */
  public function index(){
    $collcntNms = Collection::all();
    // check if the user is admin
    return view('admin/collection')->with('collcntNms',$collcntNms);
  }

  /**
  * Create the database entry into the database
  */
  public function create(Request $request){
    //Customize the error messages
    $messages = array(
      'clctnName.required' => 'Please enter a collection name',
      'clctnName.unique' => 'The collection name has already been taken by current or disabled collection',
      'clctnName.max' => 'The collection name cannot exceed 30 characters',
      'clctnName.min' => 'The collection name should be 6 characters or more',
      'clctnName.alpha_num' => 'The collection name can only have alphab',
    );

    //Rules for validation
    $rules = array(
      'clctnName' => 'required|unique:collections|max:30|min:6|alpha_num',
    );

    // Validate the request before storing the data
    $this->validate($request,$rules,$messages);

    // Create the collection name
    $thisClctn = new Collection;
    $thisClctn->clctnName = $request->clctnName;
    $thisClctn->save();

    // Take the form object and insert using model
    return redirect()->route('collectionIndex');
  }

  /**
  * Edit the database entry into the database
  */
  public function edit(Request $request){
    //Customize the error messages
    $messages = array(
      'clctnName.required' => 'Please enter a collection name',
      'clctnName.unique' => 'The collection name has already been taken by current or disabled collection',
      'clctnName.max' => 'The collection name cannot exceed 30 characters',
      'clctnName.min' => 'The collection name should be 6 characters or more',
      'clctnName.alpha_num' => 'The collection name can only have alphab',
    );

    //Rules for validation
    $rules = array(
      'clctnName' => 'required|unique:collections|max:30|min:6|alpha_num',
    );

    // Validate the request before storing the data
    $this->validate($request,$rules,$messages);

    // Create the collection name
    $thisClctn = Collection::findorFail($request->id);
    $thisClctn->clctnName = $request->clctnName;
    $thisClctn->save();

    // Take the form object and insert using model
    return redirect()->route('collectionIndex');
  }

  /**
  * Edit the database entry into the database
  */
  public function disable(Request $request){
    // Create the collection name
    $thisClctn = Collection::findorFail($request->id);
    if(strcasecmp($thisClctn->clctnName, $request->clctnName) == 0){
      $thisClctn->isEnabled = false;
      $thisClctn->save();

      // Take the form object and insert using model
      return redirect()->route('collectionIndex');
    }

    // Else redirect with error
    $thisErrMsg = "The collection name doesn't match";
    return redirect()->route('collectionIndex')->withErrors($thisErrMsg);
  }

  /**
  * Edit the database entry into the database
  */
  public function enable(Request $request){
    // Create the collection name
    $thisClctn = Collection::findorFail($request->id);
    $thisClctn->isEnabled = true;
    $thisClctn->save();
    return redirect()->route('collectionIndex');
  }

  /**
  * Edit the database entry into the database
  */
  public function restrict(Request $request){
    // Create the collection name
    $thisClctn = Collection::findorFail($request->id);
    $thisClctn->hasAccess = false;
    $thisClctn->save();
    return redirect()->route('collectionIndex');
  }

  /**
  * Edit the database entry into the database
  */
  public function allow(Request $request){
    // Create the collection name
    $thisClctn = Collection::findorFail($request->id);
    $thisClctn->hasAccess = true;
    $thisClctn->save();
    return redirect()->route('collectionIndex');
  }

  /**
  * Restrict the access for the given collection
  */
  public function rstrctAccs(Request $request){
    //
  }
}
