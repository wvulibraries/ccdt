<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        return view('admin/index');
      }
    }
}
