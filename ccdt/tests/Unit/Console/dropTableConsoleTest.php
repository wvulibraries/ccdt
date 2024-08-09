<?php
  # app/tests/Unit/Console/dropTableConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class DropTableTest extends TestCase
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
    public function it_has_drop_table_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\DropTable::class));
    }

    /** @test */
    public function it_drops_a_existing_table() {
        $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

        $this->assertEquals(\DB::table('tables')->count(), 1);

        $this->artisan('table:drop', ['tablename' => $this->tableName] )
             ->expectsOutput('Table Has been Deleted.');         

        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTablesAndFiles();
    }        

    /** @test */
    public function try_to_drop_a_missing_table() {
        $this->artisan('table:drop', ['tablename' => $this->tableName] )
             ->expectsOutput('Table Doesn\'t Exist.');     
    }      

  }
?>
