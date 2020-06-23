<?php
  # app/tests/Unit/controllers/ImportAdapterControllerUnitTest.php

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
    public $importAdapter;

    public function setUp(): void {
      parent::setUp();
      $this->collectionHelper = new CollectionHelper;
      $this->tableHelper = new TableHelper;
      $this->importAdapter = new ImportAdapter;
    }    

    public function testProcessEmptyFile() {
        // passing a empty file should throw an exception
        $path = './storage/app';
        $folder = 'flatfiles';
        $fileName = 'empty.csv';
        $collectionName = 'collection1';
        $tableName = 'testtable1';

        $this->testHelper->createCollectionWithTable($collectionName, $tableName);

        $emptyFile = $path.'/'.$folder.'/'.$fileName;
        touch($emptyFile);
        try {
          $this->importAdapter->process($tableName, $folder, $fileName);
        } catch (Exception $e) {
          $this->assertEquals("Cannot Import a Empty File.", $e->getMessage());
        }
        unlink($emptyFile);
        
        // drop testtable1
        \Schema::drop($tableName);

        // clear folder that was created with the collection
        File::deleteDirectory($this->filePath.'/'.$collectionName);

        // Delete Test Collection
        $this->collectionHelper->deleteCollection($collectionName);        
    }

    public function testProcessFile() {
        // passing a empty file should throw an exception
        $folder = 'files/test';
        $fileName = 'mlb_players.csv';
        $collectionName = 'collection1';
        $tableName = 'testtable1';

        // Create Collection Test Data Array
        $data = [
          'isCms' => false,
          'name' => $collectionName
        ];

        // Call collection helper create
        $thisClctn = $this->collectionHelper->create($data);

        // Call table helper create empty table
        $this->tableHelper->setupNewTable($folder, $fileName, $tableName, $thisClctn->id);

        // Call import adapter process
        $this->importAdapter->process($tableName, $folder, $fileName);

        //$this->tableHelper->importFile($folder, $fileName, $tableName, $thisClctn->id, $thisClctn->isCms);     

        // get newly created table
        $table = Table::where('id', '1')->first();

        // Assert table was created
        $this->assertEquals($table->id, '1');            
        
        // drop testtable1
        \Schema::drop($tableName);

        // clear folder that was created with the collection
        File::deleteDirectory($this->filePath.'/'.$collectionName);

        // Delete Test Collection
        $this->collectionHelper->deleteCollection($collectionName);            
    }    

  }
?>
