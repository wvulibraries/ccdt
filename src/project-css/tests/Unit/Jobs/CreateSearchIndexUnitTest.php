<?php
  # app/tests/Unit/Jobs/CreateSearchIndexUnitTest.php

  use App\Jobs\CreateSearchIndex;

  class CreateSearchIndexUnitTest extends TestCase {

    private $colName;
    private $tableName;     

    public function setUp(): void {
      parent::setUp();

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();

      // Generate Table Name
      $this->tableName = $this->testHelper->createTableName();      
    }    
    
    public function testPushingFakeJob() {
      // Fake the queue
      Queue::fake();

      // Push a job
      Queue::push(new CreateSearchIndex('NotATable'));

      // Assert the job was pushed to the queue
      Queue::assertPushed(CreateSearchIndex::class);
    }

    /** @test */
    public function test_building_search_index() {
        $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

        $this->assertEquals(\DB::table('tables')->count(), 1);

        $this->testHelper->seedTestTable($this->tableName, 100);

        (new CreateSearchIndex($this->tableName))->handle();

        $record = \DB::table($this->tableName)
                ->where('id', 1)
                ->get(); 

        // assert record srchindex is not Null
        $this->assertNotNull($record[0]->srchindex);        

        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTablesAndFiles();
    }  

  }
?>
