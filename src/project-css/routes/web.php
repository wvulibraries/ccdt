<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

/*
|--------------------------------------------------------------------------
| Home Page
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/
Auth::routes();

/*
|--------------------------------------------------------------------------
| Controller for the home page
|--------------------------------------------------------------------------
*/
Route::get('/home', 'HomeController@index');

/*
|--------------------------------------------------------------------------
| Controller for the admin page
|--------------------------------------------------------------------------
*/
Route::get('/collection', 'CollectionController@index')->middleware('admin');
