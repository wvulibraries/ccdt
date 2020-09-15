<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Collection;
use App\Models\Table;
use App\Helpers\CollectionHelper;

/**
 * The controller is responsible for showing, editing and updating the collection(s)
 * 
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
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
     *
     * @return void
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
   * Returns view for collection
   *
   * @param integer $colID - collection id
   *        
   * @author Tracy A McCormick  
   * @return \Illuminate\Http\Response
   */       
  public function show($colID) {
    // find the collection
    $thisClctn = Collection::findorFail($colID);  

    // Get all the tables of this collection
    $tbls = $thisClctn->tables()->get();

    // render collection/show page with array of tables
    // current collection id and name of collection
    return view('collection/show')->with('tbls', $tbls)
                                  ->with('colID', $colID)
                                  ->with('clctnName', $thisClctn->clctnName);
  }   
  
  /**
   * Generates response containing the collection/upload
   * page that will be shown.
   *
   * @param request $request
   *        
   * @author Tracy A McCormick 
   * @return \Illuminate\Http\Response (redirect to wizard import)
   */ 
  public function upload($colID) {
    // find the collection
    $thisClctn = Collection::findorFail($colID);  

    // render collection/upload page with current collection id 
    // and name of collection
    return view('collection/upload')->with('colID', $colID)
                                    ->with('clctnName', $thisClctn->clctnName);
  }   

  /**
   * Takes request validates the collection name and saves new collection to database
   *
   * @param request $request
   *        
   * @author Tracy A McCormick 
   * @return \Illuminate\Http\Response ( redirect to collection index )
   */ 
  public function create(Request $request) {
    // Validate the request before storing the data
    $this->validate($request, $this->rules, $this->messages);

    // Get required fields for collection
    $data = [
        'isCms' => $request->has('isCms') ? true : false,
        'name' => $request->clctnName
    ];

    // Using Collection Helper Create a new collection
    (new CollectionHelper)->create($data);

    // return a redirect response to a named route 
    // for better redirection
    return redirect()->route('collection.index');
  }

  /**
   * Takes request gets collection id and redirects
   * to the wizard import page for the specified collection
   *
   * @param request $request
   *        
   * @author Tracy A McCormick 
   * @return \Illuminate\Http\Response ( redirect to wizard import )
   */  
  public function tableCreate(Request $request) {
    // return a redirect response to import wizard 
    // route with collection id
    return redirect('admin/wizard/import/collection/'.$request->colID);
  }

  /**
   * Takes request validates the updated collection name and then updates the database
   *
   * @param request $request
   *        
   * @author Tracy A McCormick 
   * @return \Illuminate\Http\Response ( redirect back to collection page after edit )
   */
  public function edit(Request $request) {
    // find the collection
    $thisClctn = Collection::findorFail($request->id);

    // update isCms field if the collection name hasn't changed
    if ($thisClctn->clctnName == $request->clctnName) {
      // Set isCms
      $thisClctn->isCms = $request->has('isCms') ? true : false;

      // Save Updated items
      $thisClctn->save();
    }
    else {
      // Validate the request before storing the data
      $this->validate($request, $this->rules, $this->messages);

      // Get required fields for collection
      $data = [
          'isCms' => $request->has('isCms') ? true : false,
          'id' => $request->id,
          'name' => $request->clctnName
      ];

      // Using Collection Helper Update collection
      $result = (new CollectionHelper)->update($data);
    }

    // return a redirect response to collection index 
    // page using named route   
    return redirect()->route('collection.index');
  }

  /**
   * Sets the the state of the collection to disabled and updates database
   *
   * @param request $request
   *        
   * @author Tracy A McCormick 
   * @return \Illuminate\Http\Response ( redirect back to collection page after disable )
   */
  public function disable(Request $request) {
    // find the collection
    $thisClctn = Collection::findorFail($request->id);
    
    if (strcasecmp($thisClctn->clctnName, $request->clctnName) == 0) {
      (new CollectionHelper)->updateCollectionFlag($request->id, false);

      // Take the form object and insert using model
      return redirect()->route('collection.index');
    }

    // Else redirect with error
    return redirect()->route('collection.index')->withErrors("The collection name doesn't match");
  }

  /**
   * Sets the the state of the collection to enabled and updates database
   *
   * @param request $request
   *        
   * @author Tracy A McCormick    
   * @return \Illuminate\Http\Response ( redirect back to collection page after enable )
   */
  public function enable(Request $request) {
    (new CollectionHelper)->updateCollectionFlag($request->id, true);

    return redirect()->route('collection.index');
  }

  /**
   * Returns view for cms view creator
   *
   * @param integer $colID - collection id
   *        
   * @author Tracy A McCormick  
   * @return \Illuminate\Http\Response 
   */    
  public function creator($colID) {
    // find the collection
    $thisClctn = Collection::findorFail($colID);  

    if ($thisClctn->isCms) {
      // Get all the tables of this collection
      $tbls = $thisClctn->tables()->get();

      // render collection/creator page with array of tables
      // current collection id and name of collection
      return view('collection/creator')->with('tbls', $tbls)
                                       ->with('colID', $colID)
                                       ->with('clctnName', $thisClctn->clctnName);
    }

    return redirect()->back()->withErrors('Current Collection is Not a CMS Database'); 
  }  

}
