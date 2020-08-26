<?php
  # app/tests/Unit/controllers/TableControllerUnitTest.php

  use Illuminate\Support\Facades\Storage;
  use App\Http\Controllers\TableController;

  class TableControllerUnitTest extends BrowserKitTestCase
  {
    private $colName;
    private $tableName;

    public function setUp(): void {
      parent::setUp();

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();      

      // Generate Table Name
      $this->tableName = $this->testHelper->createTableName();  
    }

    protected function tearDown(): void {
      // test tables, files and folders that were created
      $this->testHelper->cleanupTestTables();        

      parent::tearDown();
    }    

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
    
    public function testStore() {
      $this->startSession();

      $request = new \Illuminate\Http\Request();

      // Generate Test Collection
      $collection = $this->testHelper->createCollection($this->colName);      

      // set flatfile name and table name
      $request->merge([
          'fltFle' => 'test.dat',
          'tblNme' => $this->tableName
      ]);      

      (new TableController)->store($request);

      $sessionMessages = $this->app['session']->pull('messages');
      $this->assertEquals($sessionMessages[0]['content'], 'test.dat has been queued for import to '.$this->tableName.' table. It will be available shortly.');
      $this->assertEquals($sessionMessages[0]['level'], 'success');
    }     

  }
?>
