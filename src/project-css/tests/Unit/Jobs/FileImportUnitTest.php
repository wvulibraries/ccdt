<?php
  # app/tests/Unit/Jobs/FileImportUnitTest.php 
  use App\Helpers\CollectionHelper;
  use App\Helpers\TableHelper;
  use App\Jobs\FileImport;
  use App\Models\Table;  

  class FileImportUnitTest extends TestCase {

    public function setUp(): void {
      parent::setUp();
      $this->collectionHelper = new CollectionHelper;
      $this->tableHelper = new TableHelper;

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();

      // Generate Table Name
      $this->tableName = $this->testHelper->createTableName();       
    }     
    
    public function testPushingFakeJob() {
      // Fake the queue
      Queue::fake();

      // Push a job
      Queue::push(new FileImport('test1', 'flatfile', '2.csv'));

      // Assert the job was pushed to the queue
      Queue::assertPushed(FileImport::class);
    }

    public function test_flat_file_import() {
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
        $this->tableHelper->setupNewTable($folder, $file, $this->tableName, $thisClctn->id);

        (new FileImport($this->tableName, $folder, $file))->handle();     

        // get newly created table
        $table = Table::where('id', '1')->first();

        // Assert table was created
        $this->assertEquals($table->recordCount(), 50000);
        
        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTablesAndFiles();        
    }          

  }
?>
