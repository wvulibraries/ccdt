<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\User;
use App\Models\Table;
use Auth;

/**
 * Home Controller renders main page and home page
 * for admin and users.
 * 
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class HomeController extends Controller {
    /**
     * Create a new controller instance.
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Auth::user()->isAdmin) return $this->showAdminView();
        return $this->showUserView();
    }

    /**
     * Show the application dashboard for user.
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */    
    private function showUserView() {
          // Collections
          $cllctns = Collection::all();

          // Compact them into one array
          $vwVars = array(
              'cllctns' => $cllctns,
          );

          // Return the view
          return view('user/index')->with($vwVars);
    }

    /**
     * Show the application dashboard for admin.
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */    
    private function showAdminView() {
        // Create stats array that is passed to the view
        $stats = array(
            'cllctCnt' => Collection::all()->count(),
            'usrCnt' => User::where('isAdmin', false)->count(),
            'admnCnt' => User::where('isAdmin', true)->count(),
            'tblCnt' => Table::all()->count(),
        );

        // Return the view
        return view('admin/index')->with($stats);
    }
}
