<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Route to the home page
Route::get('/', function () {
    return view('pages/welcome');
});

// Route to the admin page
Route::get('/admin', 'AdminController@adminHome')->name('admin');

// Routes inside the admin page
Route::group(['prefix'=>'admin'],function(){
  Route::get('dashboard', 'AdminController@dashboard')->name('dashboard');
  Route::get('importData', 'AdminController@importData')->name('import');
  Route::get('accessControl', 'AdminController@accessControl')->name('accessControl');
  Route::get('auditRecords', 'AdminController@auditRecords')->name('auditRecords');
  Route::post('importData/collection','AdminController@createCollection');
});
