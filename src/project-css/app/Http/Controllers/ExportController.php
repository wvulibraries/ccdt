<?php

namespace App\Http\Controllers;

use Auth;

/**
 * Export Controller renders views related to exporting
 * tables and/or collections to csv files.
 * 
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class ExportController extends Controller
{
    public function __construct() {
      // Protection to make sure this is only accessible to admin
      $this->middleware('admin');
    } 

    /**
     * Returns view for the Export Wizard Main Page
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response ( view wizard export page )
     */     
    public function export() {
        return view('admin/wizard/export')->with('AuthUsr', Auth::user());
    }  
    
    /**
     * Returns view for the Export Table Page
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response ( view wizard export table page )
     */  
    public function exportTable() {
        return view('admin/wizard/export/table')->with('AuthUsr', Auth::user());
    }    

    /**
     * Returns view for the Export Collection Page
     *
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response ( view wizard export collection page )
     */    
    public function exportCollection() {
        return view('admin/wizard/export/collection')->with('AuthUsr', Auth::user());
    }     


}



