<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

/**
 * User Controller is responsible for showing, editing 
 * and updating the users.
 * 
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class UserController extends Controller {
    /**
     * Create a new controller instance.
     */
    public function __construct() {
        $this->middleware('admin');
    }

    /**
     * Show the collection index page
     * 
     * @return \Illuminate\Http\Response
     */
    public function index() {
        // Get all the users
        $usrs = User::all();
        // Sent the current authenticated user
        $AuthUsr = Auth::user();
        // check if the user is admin
        return view('admin/users')->with('usrs', $usrs)->with('AuthUsr', $AuthUsr);
    }

    /**
     * Restrict the access for the user
     *
     * @param Request $request
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */ 
    public function restrict(Request $request) {
        // Find the user
        $thisUsr = User::findOrFail($request->userRestrictId);
        // Set the permissions
        $thisUsr->hasAccess = false;
        // Save the user
        $thisUsr->save();
        // Redirect back
        return redirect()->route('userIndex');
    }

    /**
     * Edit the database entry into the database
     *
     * @param Request $request
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */ 
    public function allow(Request $request) {
        // Create the collection name
        $thisUsr = User::findOrFail($request->userAllowId);
        $thisUsr->hasAccess = true;
        $thisUsr->save();
        return redirect()->route('userIndex');
    }

    /**
     * Make an user as admin
     *
     * @param Request $request
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */ 
    public function promote(Request $request) {
        // Create the collection name
        $thisUsr = User::findOrFail($request->userPromoteId);
        if (strcasecmp($thisUsr->name, $request->name) == 0) {
          $thisUsr->isAdmin = true;
          $thisUsr->save();
          return redirect()->route('userIndex');
        }

        // Error message
        return redirect()->route('userIndex')->withErrors("Failed to Promote User to Admin. User wasn't found.");
    }

    /**
     * Make an user as admin
     *
     * @param Request $request
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */ 
    public function demote(Request $request) {
        // Create the collection name
        $thisUsr = User::findOrFail($request->userDemoteId);
        if (strcasecmp($thisUsr->name, $request->name) == 0) {
          $thisUsr->isAdmin = false;
          $thisUsr->save();
          return redirect()->route('userIndex');
        }

        // Error message
        return redirect()->route('userIndex')->withErrors("Failed to Demote Admin to User. Admin wasn't found.");
    }

    /**
     * Return user from the $request    
     *
     * @param Request $request
     * @return object
     */     
    public function AuthRouteAPI(Request $request){
        return $request->user();
    }    

}
