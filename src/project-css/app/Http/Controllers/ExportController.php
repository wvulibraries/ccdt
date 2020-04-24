<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Http\Controllers;

use Auth;

class ExportController extends Controller
{
    public function __construct() {
      // Protection to make sure this is only accessible to admin
      $this->middleware('admin');
    } 

   /**
    * Returns view for the Export Wizard Main Page
    *
    * @return view
    */    
    public function export() {
        return view('admin/wizard/export')->with('AuthUsr', Auth::user());
    }  
    
    public function exportTable() {
        return view('admin/wizard/export/table')->with('AuthUsr', Auth::user());
    }    

    public function exportCollection() {
        return view('admin/wizard/export/collection')->with('AuthUsr', Auth::user());
    }     


}



