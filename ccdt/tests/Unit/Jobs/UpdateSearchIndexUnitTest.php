<?php
  # app/tests/Unit/Jobs/UpdateSearchIndexUnitTest.php

  use App\Jobs\CreateSearchIndex;
  use App\Jobs\UpdateSearchIndex;
  
  class UpdateSearchIndexUnitTest extends TestCase {
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
      Queue::push(new UpdateSearchIndex('NotATable', []));

      // Assert the job was pushed to the queue
      Queue::assertPushed(UpdateSearchIndex::class);
    }

    /** @test */
    public function test_updating_search_index() {
      $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

      $this->assertEquals(\DB::table('tables')->count(), 1);

      $this->testHelper->seedTestTable($this->tableName, 100);

      (new CreateSearchIndex($this->tableName))->handle();

      $records = \DB::table($this->tableName)->get(); 

      // save current srchIndex
      $srchIndex = $records[0]->srchindex;

      // add keywords and duplicate words that should be removed from search index
      $records[0]->srchindex = $srchIndex . ' about ' . $srchIndex;

      // run update
      (new UpdateSearchIndex($this->tableName, $records))->handle();

      // get updated record
      $record = \DB::table($this->tableName)
        ->where('id', 1)
        ->get(); 

      // duplicate words in the srchindex should be stripped out  
      $this->assertEquals($srchIndex, $record[0]->srchindex);
    }       

  }
?>
