<?php
  # app/tests/Unit/controllers/TableControllerUnitTest.php

  use Illuminate\Support\Facades\Storage;
  use App\Http\Controllers\TableController;

  class TableControllerUnitTest extends BrowserKitTestCase
  {
    public function testLoad() {
        // calling load should return the list of files
        // in the flatfile directory under Storage
        // we are testing that the array is present and valid
        $response = (new TableController)->load();
        $this->assertIsArray($response->fltFleList);
    }

    public function testLoadisForwardTrue() {
        // calling load and setting isForward to true
        $response = (new TableController)->load(true);
        $this->assertIsArray($response->fltFleList);
    }    

  }
?>
