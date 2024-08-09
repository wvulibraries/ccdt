<?php
  # app/tests/Unit/controllers/UploadControllerUnitTest.php

  use App\Http\Controllers\UploadController;

  class UploadControllerUnitTest extends TestCase
  {
    private $colName;
    private $tableName;     

    public function setUp(): void {
      parent::setUp();

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();
    }    

    protected function tearDown(): void {
      // Delete Test Collections
      $this->testHelper->deleteTestCollections();  
        
      // Generate Table Name
      $this->tableName = $this->testHelper->createTableName();        

      parent::tearDown();
    }        

    public function testStoreFilesInvalidCollectionId() {
      $this->expectException(Illuminate\Database\Eloquent\ModelNotFoundException::class);

      // testing store with invalid collection id should always throw a exception
      $request = new \Illuminate\Http\Request();
      (new UploadController)->storeFiles($request, 1);
    }   

    public function testStoreFilesNoAttachedFiles() {
      $this->startSession();

      // Generate Test Collection
      $collection = $this->testHelper->createCollection($this->colName);

      // testing store with invalid collection id should always throw a exception
      $request = new \Illuminate\Http\Request();
      (new UploadController)->storeFiles($request, 1);

      $sessionMessages = $this->app['session']->pull('messages');
      $this->assertEquals($sessionMessages[0]['content'], ' Error: No Files Attached');
      $this->assertEquals($sessionMessages[0]['level'], 'warning');
    }    

  }
?>
