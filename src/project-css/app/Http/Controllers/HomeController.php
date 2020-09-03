<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\User;
use App\Models\Table;
use Auth;

/**
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
     * @return \Illuminate\Http\Response
     */
    public function index() {
        if (Auth::user()->isAdmin) return $this->showAdminView();
        return $this->showUserView();
    }

    /**
     * Show the application dashboard for user.
     *
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
     * @return \Illuminate\Http\Response
     */    
    private function showAdminView() {
        // Get the count of variables
        $cllctCnt = Collection::all()->count();
        $usrCnt = User::where('isAdmin', false)->count();
        $admnCnt = User::where('isAdmin', true)->count();
        $tblCnt = Table::all()->count();

        // Compact them into array
        $stats = array(
            'cllctCnt' => $cllctCnt,
            'usrCnt' => $usrCnt,
            'admnCnt' => $admnCnt,
            'tblCnt' => $tblCnt,
        );

        // Return the view
        return view('admin/index')->with($stats);
    }
}
