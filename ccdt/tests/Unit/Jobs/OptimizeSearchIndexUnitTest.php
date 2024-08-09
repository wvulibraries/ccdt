<?php
  # app/tests/Unit/Jobs/OptimizeSearchIndexUnitTest.php

  use App\Jobs\CreateSearchIndex;
  use App\Jobs\OptimizeSearchIndex;
  use App\Jobs\OptimizeSearch;

  class OptimizeSearchIndexUnitTest extends TestCase {

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
      Queue::push(new OptimizeSearchIndex($this->tableName));

      // Assert the job was pushed to the queue
      Queue::assertPushed(OptimizeSearchIndex::class);
    }

    /** @test */
    public function test_optimize_search_index() {
      $this->testHelper->createCollectionWithTableAndRecords($this->colName, $this->tableName);

      $this->assertEquals(\DB::table('tables')->count(), 1);

      (new CreateSearchIndex($this->tableName))->handle(); 

      // OptimizeSearchIndex will dispatch Job(s) of OptimizeSearch
      // number of jobs depend on how many records are in the table
      (new OptimizeSearchIndex($this->tableName))->handle();

      // Assert 1 job has deployed
      $this->assertEquals(\DB::table('jobs')->count(), 1);
    }     

  }
?>
