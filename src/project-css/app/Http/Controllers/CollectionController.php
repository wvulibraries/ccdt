<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collection;

class CollectionController extends Controller
{
  /**
   * Where to redirect users after Creating / selecting collections.
   *
   * @var string
   */
  protected $redirectTo = '/home';

  // Customize the error messages
  private $messages = array(
      'clctnName.required' => 'Please enter a collection name',
      'clctnName.unique' => 'The collection name has already been taken by current or disabled collection',
      'clctnName.max' => 'The collection name cannot exceed 30 characters',
      'clctnName.min' => 'The collection name should be 6 characters or more',
      'clctnName.alpha_num' => 'The collection name can only have alphab',
    );

  // Rules for validation
  private $rules = array(
      'clctnName' => 'required|unique:collections|max:30|min:6|alpha_num',
    );

  /**
   * Create a new controller instance.
   */
  public function __construct()
  {
      $this->middleware('admin');
  }

  /**
   * Display a listing of the resource.
   * 
   * @return \Illuminate\Http\Response
   */
  public function index() {
    $collcntNms = Collection::all();
    // check if the user is admin
    return view('admin/collection')->with('collcntNms', $collcntNms);
  }

  /**
  * Takes request validates the collection name and saves new collection to database
  */
  public function create(Request $request) {
    // Validate the request before storing the data
    $this->validate($request, $this->rules, $this->messages);

    // Create the collection name
    $thisClctn = new Collection;
    $thisClctn->clctnName = $request->clctnName;
    $thisClctn->save();

    // Take the form object and insert using model
    // Used a named route for better redirection
    return redirect()->route('collection.index');
  }

  /**
  * Takes request validates the updated collection name and then updates the database
  */
  public function edit(Request $request) {
    // Validate the request before storing the data
    $this->validate($request, $this->rules, $this->messages);

    // Create the collection name
    $thisClctn = Collection::findorFail($request->id);
    $thisClctn->clctnName = $request->clctnName;
    $thisClctn->save();

    // Take the form object and insert using model
    return redirect()->route('collection.index');
  }

  /**
  * Sets the the state of the collection to disabled and updates database
  */
  public function disable(Request $request) {
    // find the collection
    $thisClctn = Collection::findorFail($request->id);
    
    if (strcasecmp($thisClctn->clctnName, $request->clctnName) == 0) {
      $this->updateCollectionFlag($request->id, false);

      // Take the form object and insert using model
      return redirect()->route('collection.index');
    }

    // Else redirect with error
    return redirect()->route('collection.index')->withErrors("The collection name doesn't match");
  }

  /**
  * Sets the the state of the collection to enabled and updates database
  */
  public function enable(Request $request) {
    $this->updateCollectionFlag($request->id, true);

    return redirect()->route('collection.index');
  }

  /**
  * Sets the the state of the collection to the value in $flag
  * then calls updateTableAccess to update all tables in the 
  * collection
  */
  private function updateCollectionFlag($id, $flag) {
    // Create the collection name
    $thisClctn = Collection::findorFail($id);

    // Updated all Tables in collection
    $this->updateTableAccess($thisClctn, $flag);

    # enable the collection
    $thisClctn->isEnabled = $flag;
    $thisClctn->save();
  }

  /**
  * Sets hasAccess on all tables in collection
  */  
  private function updateTableAccess($collection, $access) {
    // Get all the tables of this collection
    $thisClctnTbls = $collection->tables()->get();

    // Update all the tables of this collection
    foreach ($thisClctnTbls as $tbl) {
      $tbl->hasAccess = $access;
      $tbl->save();
    }
  }

}
