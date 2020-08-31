<?php
  # app/tests/Unit/controllers/DataViewControllerUnitTest.php

  use App\Adapters\ImportAdapter;
  use App\Helpers\FileViewHelper;
  use App\Http\Controllers\DataViewController;
  use App\Models\Table;
  
  class DataViewControllerUnitTest extends TestCase
  {
    protected $fileViewHelper;
    private $colName;  
    private $tableName; 

    public function setUp(): void {
      parent::setUp();

      $this->fileViewHelper = new FileViewHelper;

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();  

      // Generate Table Name
      $this->tableName = $this->testHelper->createTableName();          
    } 
    
    protected function tearDown(): void {
      // test tables, files and folders that were created
      $this->testHelper->cleanupTestTablesAndFiles(); 

      // Delete Test Collections
      $this->testHelper->deleteTestCollections();   
      parent::tearDown();
    }       

    public function testGetFile() {
        // Generate Test Collection
        $collection = $this->testHelper->createCollection($this->colName, 0);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection, 'test.dat');

        // Call import adapter process
        $importAdapter = (new ImportAdapter($tableName, 'files/test', 'test.dat'));
        $importAdapter->process();  

        //get table
        $table = Table::where('tblNme', $tableName)->first();
        
        // set fake filename to look for
        $testUpload = '000007.txt';        

        $folder = 'indivletters';

        mkdir('./storage/app'.'/'.$collection->clctnName.'/'.$folder);

        // create empty file
        touch('./storage/app'.'/'.$collection->clctnName.'/'.$folder.'/'.$testUpload, time() - (60 * 60 * 24 * 5));

        $response = (new DataViewController)->view($table->id, 1, $testUpload);
        $this->assertNotNull($response);
    }

  }
?>
