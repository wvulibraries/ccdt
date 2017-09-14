<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class UserController extends Controller{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct(){
      $this->middleware('admin');
  }

  /**
  * Show the collection index page
  */
  public function index(){
    // Get all the users
    $usrs = User::all();
    // Sent the current authenticated user
    $AuthUsr = Auth::user();
    // check if the user is admin
    return view('admin/users')->with('usrs',$usrs)->with('AuthUsr',$AuthUsr);
  }

  /**
  * Restrict the access for the user
  */
  public function restrict(Request $request){
    // Find the user
    $thisUsr = User::findorFail($request->id);
    // Set the permissions
    $thisUsr->hasAccess = false;
    // Save the user
    $thisUsr->save();
    // Redirect back
    return redirect()->route('userIndex');
  }

  /**
  * Edit the database entry into the database
  */
  public function allow(Request $request){
    // Create the collection name
    $thisUsr = User::findorFail($request->id);
    $thisUsr->hasAccess = true;
    $thisUsr->save();
    return redirect()->route('userIndex');
  }

  /**
  * Make an user as admin
  */
  public function promote(Request $request){
    // Create the collection name
    $thisUsr = User::findorFail($request->id);
    if(strcasecmp($thisUsr->name, $request->name) == 0){
      $thisUsr->isAdmin = true;
      $thisUsr->save();
      return redirect()->route('userIndex');
    }

    // Error message
    $thisErrMsg = "The user names doesn't match";
    return redirect()->route('userIndex')->withErrors($thisErrMsg);
  }

  /**
  * Make an user as admin
  */
  public function demote(Request $request){
    // Create the collection name
    $thisUsr = User::findorFail($request->id);
    if(strcasecmp($thisUsr->name, $request->name) == 0){
      $thisUsr->isAdmin = false;
      $thisUsr->save();
      return redirect()->route('userIndex');
    }

    // Error message
    $thisErrMsg = "The user names doesn't match";
    return redirect()->route('userIndex')->withErrors($thisErrMsg);
  }

}
