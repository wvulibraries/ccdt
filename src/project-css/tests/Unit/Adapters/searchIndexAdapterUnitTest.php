<?php
  # app/tests/Unit/Adapters/searchIndexAdapterControllerUnitTest.php

  use Illuminate\Support\Facades\Storage;
  use App\Adapters\ImportAdapter;
  use App\Adapters\SearchIndexAdapter;  
  use App\Models\Collection;
  use App\Models\Table;

  class SearchIndexAdapterUnitTest extends BrowserKitTestCase
  {
    public $searchAdapter;

    private $colName;
    private $tableName;     

    public function setUp(): void {
      parent::setUp();
      $this->searchAdapter = new SearchIndexAdapter;

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

        parent::tearDown();
    }     

    public function testProcessTable() {
        $this->testHelper->createCollectionWithTableAndRecords($this->colName, $this->tableName);

        // get first record in table verify srchindex is blank
        $record = \DB::table($this->tableName)
                ->where('id', 1)
                ->get(); 

        // assert record srchindex is blank
        $this->assertNull($record[0]->srchindex);   

        // process table inserting srchindex for each records
        $this->searchAdapter->process($this->tableName);

        $record = \DB::table($this->tableName)
                ->where('id', 1)
                ->get(); 
                
        // assert record srchindex is blank
        $this->assertEquals($record[0]->srchindex, strtolower($record[0]->firstname . ' ' . $record[0]->lastname));      
    } 

  }
?>
