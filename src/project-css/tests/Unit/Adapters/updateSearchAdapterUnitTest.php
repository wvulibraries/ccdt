<?php
  # app/tests/Unit/Adapters/updateSearchAdapterControllerUnitTest.php

  use Illuminate\Support\Facades\Storage;
  use App\Adapters\ImportAdapter;
  use App\Adapters\SearchIndexAdapter; 
  use App\Adapters\UpdateSearchAdapter;
  use App\Models\Collection;
  use App\Models\Table;

  class UpdateSearchAdapterUnitTest extends BrowserKitTestCase
  {
    // location of test files
    private $filePath = './storage/app';
    public $searchAdapter;
    public $updateSearchAdapter;

    private $colName;
    private $tableName;    

    public function setUp(): void {
      parent::setUp();
      $this->searchAdapter = new SearchIndexAdapter;
      $this->updateSearchAdapter = new UpdateSearchAdapter;

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();

      // Generate Table Name
      $this->tableName = $this->testHelper->createTableName();       
    } 
    
    protected function tearDown(): void {
      // test tables, files and folders that were created
      $this->testHelper->cleanupTestTablesAndFiles([]);

      // Delete Test Collection
      $this->testHelper->deleteCollection($this->colName);         

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

      // assert record srchindex has only first name and lastname that is lowercase separated by a space
      $this->assertEquals($record[0]->srchindex, strtolower($record[0]->firstname . ' ' . $record[0]->lastname));                 

      // append additional items that should be stripped out of the search index
      $this->updateSearchAdapter->process($this->tableName, $record[0]->id, $record[0]->srchindex . ' across is a e v .. $');

      $record = \DB::table($this->tableName)
                ->where('id', 1)
                ->get(); 

      // assert record srchindex has only first name and lastname that is lowercase separated by a space
      $this->assertEquals($record[0]->srchindex, strtolower($record[0]->firstname . ' ' . $record[0]->lastname));           
    } 

  }
?>
