<?php
  # app/tests/Unit/controllers/UploadControllerUnitTest.php

  use App\Http\Controllers\UploadController;

  class UploadControllerUnitTest extends TestCase {

    public function setUp() {
         parent::setUp();
         Artisan::call('migrate');

         \DB::insert('insert into collections (clctnName, isEnabled, hasAccess) values(?, ?, ?)',['collection1', true, true]);

         //insert record into table for testing
         \DB::insert('insert into tables (tblNme, collection_id, hasAccess) values(?, ?, ?)',['testtable1', 1, true]);
    }

    protected function tearDown() {
          Artisan::call('migrate:reset');
          parent::tearDown();
    }

    public function testStoreFiles() {
          // testing store with no attached items should always fail
          $request = new \Illuminate\Http\Request();
          $this->assertFalse((new UploadController)->storeFiles($request, 1));
    }

  }
?>
