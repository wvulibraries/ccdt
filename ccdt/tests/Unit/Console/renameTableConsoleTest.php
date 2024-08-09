<?php
  # app/tests/Unit/Console/renameTableConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class RenameTableTest extends TestCase
  {
    private $colName;  
    private $tableName;
    private $tableName2;    
    
    public function setUp(): void {
      parent::setUp();

      $this->helper = new CollectionHelper; 

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();  

      // Generate Table Name
      $this->tableName = $this->testHelper->createTableName(); 
      $this->tableName2 = $this->tableName . '1';        
    } 
    
    protected function tearDown(): void {
        // Delete Test Collections
        $this->testHelper->deleteTestCollections();   
        parent::tearDown();
    }    

    /** @test */
    public function it_has_rename_table_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\RenameTable::class));
    }

    /** @test */
    public function it_rename_a_existing_table() {
        $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

        $this->assertEquals(\DB::table('tables')->count(), 1);

        $this->artisan('table:rename', ['tablename' => $this->tableName, 'newname' => $this->tableName2] )
             ->expectsOutput('Table Has been Renamed.');         

        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTablesAndFiles();
    }  
    
    /** @test */
    public function try_to_rename_a_table_with_existing_name() {
        $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

        $this->assertEquals(\DB::table('tables')->count(), 1);

        $this->artisan('table:rename', ['tablename' => $this->tableName, 'newname' => $this->tableName] )
             ->expectsOutput($this->tableName . ' Already Exists.');  
             
        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTablesAndFiles();             
    }     

    /** @test */
    public function try_to_rename_a_nonexisting_table() {
        $this->artisan('table:rename', ['tablename' => $this->tableName, 'newname' => $this->tableName2] )
             ->expectsOutput('Table Doesn\'t Exist.');     
    }      

  }
?>
