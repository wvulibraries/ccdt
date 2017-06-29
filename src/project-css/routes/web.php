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
Route::get('home', 'HomeController@index')->name('home');

/*
|--------------------------------------------------------------------------
| Controller for the admin dashboard page
|--------------------------------------------------------------------------
*/
Route::get('collection', 'CollectionController@index')->name('collectionIndex');
Route::group(['prefix' => 'collection'], function(){
  Route::post('create', 'CollectionController@create');
  Route::post('edit', 'CollectionController@edit');
  Route::post('disable', 'CollectionController@disable');
  Route::post('enable', 'CollectionController@enable');
});

/*
|--------------------------------------------------------------------------
| Controller for the tables homepage
|--------------------------------------------------------------------------
*/
Route::get('table', 'TableController@index')->name('tableIndex');
Route::group(['prefix' => 'table'], function(){
  Route::get('create', 'TableController@wizard');
  Route::post('create/import', 'TableController@import');
  Route::post('create/select', 'TableController@select');
  // Forward route in case for error
  Route::get('create/select','TableController@wizard');
  Route::post('create/finalize', 'TableController@finalize');
  Route::get('load','TableController@load');
  Route::post('load/worker','TableController@worker');
  Route::post('restrict', 'TableController@restrict');
});

/*
|--------------------------------------------------------------------------
| Controller for the user management
|--------------------------------------------------------------------------
*/
Route::get('users', 'UserController@index')->name('userIndex');
Route::group(['prefix' => 'user'], function(){
  Route::post('restrict', 'UserController@restrict');
  Route::post('allow', 'UserController@allow');
  Route::post('promote', 'UserController@promote');
  Route::post('demote', 'UserController@demote');
});

/*
|--------------------------------------------------------------------------
| Controller for the collection records
|--------------------------------------------------------------------------
*/
Route::get('data/{curTable}','DataViewController@index')->name('dataIndex');
Route::get('data/{curTable}/{id}','DataViewController@show')->name('dataShow');
Route::post('data/{curTable}','DataViewController@search')->name('dataSearch');
Route::get('data/{curTable}/search/{search}','DataViewController@search')->name('dataSearch');
