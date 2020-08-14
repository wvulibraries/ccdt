<?php
  # app/tests/Unit/Jobs/OptimizeSearchUnitTest.php

  use App\Jobs\CreateSearchIndex;
  use App\Jobs\OptimizeSearch;

  class OptimizeSearchUnitTest extends TestCase {

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
      $this->testHelper->cleanupTestTablesAndFiles();

      // Delete Test Collections
      $this->testHelper->deleteTestCollections();         

      parent::tearDown();
    }     
    
    public function testPushingFakeJob() {
      // Fake the queue
      Queue::fake();

      // Push a job
      Queue::push(new OptimizeSearch($this->tableName, 0, 500));

      // Assert the job was pushed to the queue
      Queue::assertPushed(OptimizeSearch::class);
    }    

    /** @test */
    public function test_optimize_search() {
      $this->testHelper->createCollectionWithTableAndRecords($this->colName, $this->tableName);

      $this->assertEquals(\DB::table('tables')->count(), 1);

      (new CreateSearchIndex($this->tableName))->handle();

      $record = \DB::table($this->tableName)
        ->where('id', 1)
        ->get(); 

      // save current srchIndex
      $srchIndex = $record[0]->srchindex;

      // duplicate srchindex on record 1
      \DB::table($this->tableName)
        ->where('id', 1)
        ->update(['srchindex' => $srchIndex . ' ' . $srchIndex]);

      (new OptimizeSearch($this->tableName, 0, 500))->handle();

      // get updated record
      $record = \DB::table($this->tableName)
        ->where('id', 1)
        ->get(); 

      // duplicate words in the srchindex should be stripped out  
      $this->assertEquals($srchIndex, $record[0]->srchindex);
    }    

  }
?>
