<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function adminHome(){
    return view('pages/admin/dashboard');
  }

  /**
  * Function for the import data view
  */
  public function dashboard(){
    return view('pages/admin/dashboard');
  }
}
