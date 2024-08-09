<?php

Route::view('/', 'welcome')->name('home');

// Authentication Routes
Auth::routes();

// Home Page
Route::get('home', 'HomeController@index')->name('home.index');

// Help Page
Route::view('/help', 'help')->name('help.index');

// Collection Routes
Route::prefix('collection')->name('collection.')->group(function() {
    Route::get('/', 'CollectionController@index')->name('index');
    Route::post('create', 'CollectionController@create')->name('create');
    Route::post('edit', 'CollectionController@edit')->name('edit');
    Route::get('show/{colID}', 'CollectionController@show')->name('show'); 
    Route::get('upload/{colID}', 'CollectionController@upload')->name('upload');
    Route::get('{colID}/table/create', 'CollectionController@tableCreate')->name('table.create'); 
    Route::post('disable', 'CollectionController@disable')->name('disable');
    Route::post('enable', 'CollectionController@enable')->name('enable');
});

// Table Routes
Route::prefix('table')->name('table.')->group(function() {
    Route::get('/', 'TableController@index')->name('index');
    Route::get('edit/{curTable}', 'TableController@edit')->name('edit');
    Route::post('update', 'TableController@update')->name('update');
    Route::get('edit/schema/{curTable}', 'TableController@editSchema')->name('edit.schema');
    Route::post('update/schema', 'TableController@updateSchema')->name('update.schema');
    Route::get('create', 'TableController@create')->name('create');
    Route::post('create/finalize', 'TableController@finalize')->name('create.finalize');
    Route::get('load', 'TableController@load')->name('load');
    Route::post('load/store', 'TableController@store')->name('load.store');
    Route::post('restrict', 'TableController@restrict')->name('restrict');
});

// Admin Wizard Routes
Route::prefix('admin/wizard')->name('wizard.')->group(function() {
    Route::get('import/collection/{colID}', 'WizardController@importCollection')->name('import.collection');
    Route::get('flatfile', 'WizardController@flatfile')->name('flatfile');
    Route::post('flatfile/upload', 'WizardController@flatfileUpload')->name('flatfile.upload');
    Route::post('flatfile/select', 'WizardController@flatfileSelect')->name('flatfile.select');  
    Route::get('cms', 'WizardController@cms')->name('cms');
    Route::post('cms/upload', 'WizardController@cmsUpload')->name('cms.upload');
    Route::post('cms/select', 'WizardController@cmsSelect')->name('cms.select');    
});

// User Management Routes
Route::prefix('user')->name('user.')->group(function() {
    Route::get('/', 'UserController@index')->name('index');
    Route::post('restrict', 'UserController@restrict')->name('restrict');
    Route::post('allow', 'UserController@allow')->name('allow');
    Route::post('promote', 'UserController@promote')->name('promote');
    Route::post('demote', 'UserController@demote')->name('demote');
    Route::post('reset', 'UserController@reset')->name('reset');
});

// Data Management Routes with checktableid Middleware
Route::middleware(['checktableid'])->prefix('data')->name('data.')->group(function() {
    Route::get('{curTable}', 'DataViewController@index')->name('index');
    Route::get('{curTable}/{id}', 'DataViewController@show')->name('show');
    Route::post('{curTable}', 'DataViewController@search')->name('search');
    Route::get('{curTable}/search/{page}', 'DataViewController@search')->name('search.page');
    Route::get('{curTable}/{recId}/view/{key}/{filename}', 'DataViewController@view')->name('file.view.key');
    Route::get('{curTable}/{recId}/view/{filename}', 'DataViewController@view')->name('file.view');
});

// Collection Uploads Routes with checkcollectionid Middleware
Route::middleware(['checkcollectionid'])->prefix('upload')->name('upload.')->group(function() {
    Route::get('{curCol}', 'UploadController@index')->name('index');
    Route::post('{curCol}', 'UploadController@storeFiles')->name('store');
});

// Admin Jobs Routes
Route::prefix('admin/jobs')->name('jobs.')->group(function() {
    Route::get('pending', 'JobsController@pending')->name('pending');
    Route::get('failed', 'JobsController@failed')->name('failed');
    Route::get('retry/{id}', 'JobsController@retry')->name('retry');
    Route::get('retryall', 'JobsController@retryAll')->name('retry.all');
    Route::get('forget/{id}', 'JobsController@forget')->name('forget');
    Route::get('flush', 'JobsController@flush')->name('flush');
});
