<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectionController extends Controller
{
  /**
  * Show the collection index page
  */
  public function index(){
    // check if the user is admin
    return 'Collection';
  }
}
