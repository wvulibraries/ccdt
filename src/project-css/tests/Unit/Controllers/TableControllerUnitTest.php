<?php
  # app/tests/Unit/controllers/TableControllerUnitTest.php

  use Illuminate\Support\Facades\Storage;
  use App\Http\Controllers\TableController;

  class TableControllerUnitTest extends TestCase {

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

    public function testLoad() {
        // calling load should return the list of files
        // in the flatfile directory under Storage
        // we are testing that the array is present and valid
        $response = (new TableController)->load();
        $this->assertInternalType('array', $response->fltFleList);
    }

    public function testProcessEmptyFile() {
        // passing a empty file should throw an exception
        $emptyFile = './storage/app/flatfiles/empty.csv';
        touch($emptyFile);
        try {
          (new TableController)->process('testtable1', 'empty.csv');
        } catch (Exception $e) {
          $this->assertEquals("Cannot Import a Empty File.", $e->getMessage());
        }
        unlink($emptyFile);
    }


  }
?>
