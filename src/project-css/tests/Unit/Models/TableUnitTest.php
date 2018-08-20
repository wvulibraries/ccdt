<?php
  # app/tests/Unit/controllers/StopWordsUnitTest.php

  // use App\Http\Controllers\JobsController;
  // use App\Http\Controllers\TableController;
  use App\Models\Table;

  class TableUnitTest extends TestCase {

    public function setUp() {
         parent::setUp();
         Artisan::call('migrate');
         Artisan::call('db:seed');
    }

    protected function tearDown() {
         Artisan::call('migrate:reset');
         parent::tearDown();
    }


  }
?>
