<?php
  # app/tests/Unit/middleware/CheckTableIdUnitTest.php

  use App\Http\Middleware\CheckTableId;

  class CheckTableIdUnitTest extends BrowserKitTestCase {

    public function setUp(): void {
         parent::setUp();
         //Artisan::call('migrate');

         \DB::insert('insert into collections (clctnName, isEnabled, hasAccess) values(?, ?, ?)',['collection1', true, true]);

         //insert record into table for testing
         \DB::insert('insert into tables (tblNme, collection_id, hasAccess) values(?, ?, ?)',['testtable1', 1, true]);
         \DB::insert('insert into tables (tblNme, collection_id, hasAccess) values(?, ?, ?)',['testtable2', 1, false]);
    }

    // protected function tearDown(): void {
    //      Artisan::call('migrate:reset');
    //      parent::tearDown();
    // }

    public function testIsValidTable() {
        $response =  (new CheckTableId)->isValidTable('invalid');
        $this->assertFalse($response);
        $response =  (new CheckTableId)->isValidTable(0);
        $this->assertFalse($response);
        $response =  (new CheckTableId)->isValidTable(1);
        $this->assertTrue($response);
    }

    public function testHasAccess() {
        $response =  (new CheckTableId)->hasAccess('invalid');
        $this->assertFalse($response);
        $response =  (new CheckTableId)->hasAccess(1);
        $this->assertTrue($response);
        $response =  (new CheckTableId)->hasAccess(2);
        $this->assertFalse($response);
    }

  }
?>
