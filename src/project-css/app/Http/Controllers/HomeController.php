<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Collection;
use App\User;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
      // Check if the user is admin
      // Page for a normal user
      if(!(Auth::user()->isAdmin)){
        return view('user/index');
      }
      // Page for a admin user
      else{
        // Get the count of variables
        $cllctCnt = Collection::all()->count();
        $usrCnt = User::where('isAdmin',false)->count();
        $admnCnt = User::where('isAdmin',true)->count();

        // Compact them into array
        $stats = array(
          'cllctCnt' => $cllctCnt,
          'usrCnt' => $usrCnt,
          'admnCnt' => $admnCnt,
        );

        //Return the view
        return view('admin/index')->with($stats);;
      }
    }
}
