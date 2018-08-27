<?php
  # app/tests/Unit/controllers/DataViewControllerUnitTest.php

  use App\Http\Controllers\DataViewController;
  use App\Libraries\TestHelper;

  class DataViewControllerUnitTest extends TestCase {

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

    public function testView() {
          // passing a empty file should throw an exception
          $path = './storage/app';
          $folder = 'files/test';
          $file = 'test_upload.doc';
          $tableName = 'testtable1';

          \File::makeDirectory($path . '/' . $tableName);
          \File::copy($path . '/' . $folder . '/' . $file, $path . '/' . $tableName . '/' . $file);

          \Schema::connection('mysql')->create($tableName, function($table)
          {
              $table->increments('id');
              $table->string('email', 50);
              $table->integer('votes');
          });

          \DB::table($tableName)->insert(
              ['email' => 'john@example.com', 'votes' => 0]
          );

          $response = (new DataViewController)->view(1, 1, $file);

          $this->assertAttributeEquals('user.fileviewer', 'view', $response);

          // test tables, files and folders that were created
          (new TestHelper)->cleanupTestTables();
    }

  }
?>
