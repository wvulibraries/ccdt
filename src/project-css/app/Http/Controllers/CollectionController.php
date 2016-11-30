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
    // Validate the request before storing the data
    $this->validate($request,[
      'clctnName' => 'required|unique:collections|max:30|min:6|alpha_num',
    ]);

    // Create the collection name
    $thisClctn = new Collection;
    $thisClctn->clctnName = $request->clctnName;
    $thisClctn->save();

    // Take the form object and insert using model
    return redirect()->route('collectionIndex');
  }
}
