<?php
  # app/tests/Unit/Console/optimizeSrchIndexConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  
  use App\Commands\optimizeSrchIndex;

  class OptimizeSrchIndexTest extends TestCase
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
    public function it_has_optimize_search_index_command()
    {
      $this->assertTrue(class_exists(\App\Console\Commands\optimizeSrchIndex ::class));
    }

    /** @test */
    public function it_dispatches_optimize_index_job() {
        $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

        $this->assertEquals(\DB::table('tables')->count(), 1);

        $this->testHelper->seedTestTable($this->tableName, 100);

        $this->artisan('table:create:search', ['tablename' => $this->tableName] )
             ->expectsOutput('Job has been created to Create Search Index');

        $this->artisan('table:optimize:search', ['tablename' => $this->tableName] )
             ->expectsOutput('Job has been created to Optimize Search Index');  
             
        // Assert 1 job has deployed
        $this->assertEquals(\DB::table('jobs')->count(), 2);             

        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTablesAndFiles();
    }    

    /** @test */
    public function try_to_optimize_a_nonexisting_table() {
      $this->artisan('table:optimize:search', ['tablename' => $this->tableName] )
            ->expectsOutput('Table Doesn\'t Exist.');

      // Assert no jobs have been deployed
      $this->assertEquals(\DB::table('jobs')->count(), 0);
    }      

  }
?>
