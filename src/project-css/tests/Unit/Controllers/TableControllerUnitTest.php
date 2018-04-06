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

    public function testSchema() {
        if (File::exists(storage_path('/flatfiles/mlb_players.csv'))) {
          // check for a valid file
          $result = (new TableController)->schema('/files/test/mlb_players.csv');
          $this->assertEquals($result[ 0 ], 'Name');
        }

        // passing a filename that doesn't exits should produce false result
        $this->assertFalse((new TableController)->schema('/files/test/unknown.csv'));

        // passing a file that isn't of the correct type should produce false result
        $this->assertFalse((new TableController)->schema('/files/test/images.png'));

        //passing a empty file should produce a false result
        $emptyFile = './storage/app/files/test/empty.csv';
        touch($emptyFile);
        $this->assertFalse((new TableController)->schema('/files/test/empty.csv'));
        unlink($emptyFile);
    }

    public function testLoad() {
        // calling load should return items one is the list of files
        // in the flatfile directory under Storage
        // we are testing that the array is present and valid
        $response = (new TableController)->load();
        $this->assertInternalType('array', $response->fltFleList);
    }

    public function testProcess() {
        // passing a empty file should throw an exception
        $request = new \Illuminate\Http\Request();
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
