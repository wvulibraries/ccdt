<?php
  # app/tests/Unit/controllers/UploadControllerUnitTest.php

  use App\Http\Controllers\UploadController;

  class UploadControllerUnitTest extends BrowserKitTestCase
  {

    public function setUp(): void 
    {
         parent::setUp();
         //Artisan::call('migrate');
    }

    protected function tearDown(): void {
          //Artisan::call('migrate:reset');
          parent::tearDown();
    }

    // public function testStoreFiles() {
    //       // testing store with no attached items should always fail
    //       $request = new \Illuminate\Http\Request();
    //       $this->assertFalse((new UploadController)->storeFiles($request, 1));
    // }

  }
?>
