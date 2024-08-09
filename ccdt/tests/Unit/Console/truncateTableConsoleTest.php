<?php
  # app/tests/Unit/Console/truncateTableConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class TruncateTableTest extends TestCase
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
    public function it_has_truncate_table_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\TruncateTable::class));
    }

    /** @test */
    public function it_truncates_a_existing_table() {
        $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

        $this->assertEquals(\DB::table('tables')->count(), 1);

        $this->artisan('table:truncate', ['tablename' => $this->tableName] )
             ->expectsOutput('Table Has been Truncated.');         

        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTablesAndFiles();
    }   

    /** @test */
    public function try_to_truncate_a_nonexisting_table() {
        $this->artisan('table:truncate', ['tablename' => $this->tableName] )
             ->expectsOutput('Table Doesn\'t Exist.');     
    }      

  }
?>
