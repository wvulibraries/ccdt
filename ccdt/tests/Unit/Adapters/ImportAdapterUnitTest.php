<?php
  # app/tests/Unit/Adapters/importAdapterControllerUnitTest.php

  use Illuminate\Support\Facades\Storage;
  use App\Adapters\ImportAdapter;
  use App\Models\Collection;
  use App\Models\Table;
  use App\Helpers\CollectionHelper;
  use App\Helpers\TableHelper;

  class ImportAdapterUnitTest extends BrowserKitTestCase
  {
    // location of test files
    private $filePath = './storage/app';
    public $collectionHelper;
    public $tableHelper;

    private $colName;
    private $tableName;       

    public function setUp(): void {
      parent::setUp();
      $this->collectionHelper = new CollectionHelper;
      $this->tableHelper = new TableHelper;

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();

      // Generate Table Name
      $this->tableName = $this->testHelper->createTableName();       
    } 
    
    protected function tearDown(): void {
        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTables();

        // Delete Test Collections
        $this->testHelper->deleteTestCollections();   
        
        // Delete Test Files
        $this->testHelper->cleanupTestUploads(['empty.csv', '50000 Sales Records.csv', 'test.dat']);

        parent::tearDown();
    }    

    public function testProcessEmptyFile() {
        // passing a empty file should throw an exception
        $path = './storage/app';
        $folder = 'flatfiles';
        $file = 'empty.csv';

        $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

        $emptyFile = $path.'/'.$folder.'/'.$file;
        touch($emptyFile);
        try {
          $importAdapter = (new ImportAdapter($this->tableName, $folder, $file));
          $importAdapter->process();
        } catch (Exception $e) {
          $this->assertEquals("Cannot Import a Empty File.", $e->getMessage());
        }
        unlink($emptyFile);       
    }

    public function testProcessFileLargeTestCSV() {
        // passing a empty file should throw an exception
        $folder = 'files/test';
        $file = '50000 Sales Records.csv';

        // Create Collection Test Data Array
        $data = [
          'isCms' => false,
          'name' => $this->colName
        ];

        // Call collection helper create
        $thisClctn = $this->collectionHelper->create($data);

        // Call table helper create empty table
        $result = $this->tableHelper->setupNewTable($folder, $file, $this->tableName, $thisClctn->id);
        
        // Assert table setup succeeded
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertFalse($result['error'], 'setupNewTable failed: ' . json_encode($result));

        // Call import adapter process
        $importAdapter = (new ImportAdapter($this->tableName, $folder, $file));
        $importAdapter->process();    

        // get newly created table by name
        $table = Table::where('tblNme', $this->tableName)->first();

        // Assert table was created
        $this->assertNotNull($table);
        $this->assertEquals($table->tblNme, $this->tableName);                    
    } 

    public function testProcessFileWithSplitRecord() {
        // passing a empty file should throw an exception
        $folder = 'files/test';
        $file = 'test.dat';

        // Create Collection Test Data Array
        $data = [
          'isCms' => true,
          'name' => $this->colName
        ];

        // Call collection helper create
        $thisClctn = $this->collectionHelper->create($data);

        // Call table helper create empty table
        $result = $this->tableHelper->setupNewTable($folder, $file, $this->tableName, $thisClctn->id, true);
        
        // Assert table setup succeeded
        $this->assertIsArray($result);
        $this->assertArrayHasKey('error', $result);
        $this->assertFalse($result['error'], 'setupNewTable failed: ' . json_encode($result));

        // Setup new import adapter
        $importAdapter = (new ImportAdapter($this->tableName, $folder, $file));

        // Call import adapter process to import the file        
        $importAdapter->process();     

        // get newly created table by name
        $table = Table::where('tblNme', $this->tableName)->first();

        // Assert table was created
        $this->assertNotNull($table);
        $this->assertEquals($table->tblNme, $this->tableName);                    
    }            

  }
?>
