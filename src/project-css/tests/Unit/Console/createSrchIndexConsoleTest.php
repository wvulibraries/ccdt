<?php
  # app/tests/Unit/Console/createSrchIndexConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class CreateSrchIndexTest extends TestCase
  {
    private $colName;  
    private $tableName;   
    
    public function setUp(): void {
      parent::setUp();

      $this->helper = new CollectionHelper; 

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();  

      // Generate Table Name
      $this->tableName = $this->testHelper->createTableName();         
    } 
    
    protected function tearDown(): void {
        // Delete Test Collections
        $this->testHelper->deleteTestCollections();   
        parent::tearDown();
    }    

    /** @test */
    public function it_has_create_search_index_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\CreateSrchIndex::class));
    }

    /** @test */
    public function it_dispatches_search_index_job() {
        $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

        $this->assertEquals(\DB::table('tables')->count(), 1);

        $this->testHelper->seedTestTable($this->tableName, 100);

        $this->artisan('table:create:search', ['tablename' => $this->tableName] )
             ->expectsOutput('Job has been created to Create Search Index');         

        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTablesAndFiles();
    }  
    
    /** @test */
    public function try_to_create_index_on_nonexisting_table() {
        $this->assertEquals(\DB::table('tables')->count(), 0);        

        $this->artisan('table:create:search', ['tablename' => $this->tableName] )
             ->expectsOutput('Table Doesn\'t Exist.');     
    }      

  }
?>
