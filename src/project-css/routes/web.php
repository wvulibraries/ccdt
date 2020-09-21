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
Route::view('/', 'welcome');

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
| Help Page
|--------------------------------------------------------------------------
*/
Route::view('/help', 'help');

/*
|--------------------------------------------------------------------------
| Controller for the admin collection page
|--------------------------------------------------------------------------
*/
Route::get('collection', 'CollectionController@index')->name('collection.index');
Route::group([ 'prefix' => 'collection' ], function() {
    Route::post('create', 'CollectionController@create')->name('collection.create');
    Route::post('edit', 'CollectionController@edit')->name('collection.edit');
    Route::get('show/{colID}', 'CollectionController@show')->name('collection.show'); 
    Route::get('upload/{colID}', 'CollectionController@upload')->name('collection.upload');
    Route::get('{colID}/table/create', 'CollectionController@tableCreate')->name('collection.table.create'); 
    Route::post('disable', 'CollectionController@disable')->name('collection.disable');
    Route::post('enable', 'CollectionController@enable')->name('collection.enable');
});

/*
|--------------------------------------------------------------------------
| Controller for the tables homepage
|--------------------------------------------------------------------------
*/
Route::get('table', 'TableController@index')->name('tableIndex');
Route::group([ 'prefix' => 'table' ], function() {
    Route::get('edit/{curTable}', 'TableController@edit')->name('table.edit');
    Route::post('update', 'TableController@update');

    Route::get('edit/schema/{curTable}', 'TableController@editSchema')->name('table.edit.schema');
    Route::post('update/schema', 'TableController@updateSchema');
    Route::get('create', 'TableController@create');

    // Forward route in case for error
    Route::post('create/finalize', 'TableController@finalize');
    Route::get('load', 'TableController@load');
    Route::post('load/store', 'TableController@store');
    Route::post('restrict', 'TableController@restrict');
});

/*
|--------------------------------------------------------------------------
| Controller for the import wizard
|--------------------------------------------------------------------------
*/
Route::group([ 'prefix' => 'admin/wizard' ], function() {
    Route::get('import/collection/{colID}', 'WizardController@importCollection')->name('wizard.import.collection');

    Route::get('flatfile', 'WizardController@flatfile')->name('wizard.flatfile');
    Route::post('flatfile/upload', 'WizardController@flatfileUpload')->name('wizard.flatfile.upload');
    Route::post('flatfile/select', 'WizardController@flatfileSelect')->name('wizard.flatfile.select');  
    
    Route::get('cms', 'WizardController@cms')->name('wizard.cms');
    Route::post('cms/upload', 'WizardController@cmsUpload')->name('wizard.cms.upload');
    Route::post('cms/select', 'WizardController@cmsSelect')->name('wizard.cms.select');    
});



/*
|--------------------------------------------------------------------------
| Controller for the user management
|--------------------------------------------------------------------------
*/
Route::get('users', 'UserController@index')->name('userIndex');
Route::group([ 'prefix' => 'user' ], function() {
    Route::post('restrict', 'UserController@restrict');
    Route::post('allow', 'UserController@allow');
    Route::post('promote', 'UserController@promote');
    Route::post('demote', 'UserController@demote');
    Route::post('reset', 'UserController@reset');
});

/*
|--------------------------------------------------------------------------
| checktableid middleware group for checking for a valid table id
|--------------------------------------------------------------------------
*/
Route::group([ 'middleware' => [ 'checktableid' ] ], function() {
  /*
  |--------------------------------------------------------------------------
  | Controller for managing data views
  |--------------------------------------------------------------------------
  */
  Route::group([ 'prefix' => 'data' ], function() {
      Route::get('{curTable}', 'DataViewController@index')->name('dataIndex');
      Route::get('{curTable}/{id}', 'DataViewController@show')->name('dataShow');
      Route::post('{curTable}', 'DataViewController@search')->name('dataSearch');
      Route::get('{curTable}/search/{page}', 'DataViewController@search')->name('dataSearch');
      Route::get('{curTable}/{recId}/view/{key}/{filename}', 'DataViewController@view')->name('dataFileView');
      Route::get('{curTable}/{recId}/view/{filename}', 'DataViewController@view')->name('dataFileView');
  });

});

/*
|--------------------------------------------------------------------------
| checkcollectionid middleware group for checking for a valid collection id
|--------------------------------------------------------------------------
*/
Route::group([ 'middleware' => [ 'checkcollectionid' ] ], function() {

  /*
  |--------------------------------------------------------------------------
  | Controller for managing file uploads to collections
  |--------------------------------------------------------------------------
  */
  Route::group([ 'prefix' => 'upload' ], function() {
      Route::get('{curCol}', 'UploadController@index');
      Route::post('{curCol}', 'UploadController@storeFiles');
  });

});

/*
|--------------------------------------------------------------------------
| Admin Jobs Controller for displaying and managing the Job queue
|--------------------------------------------------------------------------
*/
Route::group([ 'prefix' => 'admin/jobs' ], function() {
    Route::get('pending', 'JobsController@pending');
    Route::get('failed', 'JobsController@failed');
    Route::get('retry/{id}', 'JobsController@retry');
    Route::get('retryall', 'JobsController@retryAll');
    Route::get('forget/{id}', 'JobsController@forget');
    Route::get('flush', 'JobsController@flush');
});
