<?php
  # app/tests/Unit/Console/importTableConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class ExportTableTest extends TestCase
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
        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTablesAndFiles(); 

        // Delete Test Collections
        $this->testHelper->deleteTestCollections();   
        parent::tearDown();
    }    

    /** @test */
    public function it_has_import_table_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\ExportTable::class));
    }

    /** @test */
    public function it_exports_a_table_to_csv_file() {
        $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

        $this->assertEquals(\DB::table('tables')->count(), 1);

        $this->testHelper->seedTestTable($this->tableName, 100);

        $this->artisan('table:export', ['tablename' => $this->tableName, '--field' => ['id']])
             ->expectsOutput('Table ' . $this->tableName . ' Has Been Exported.');         

        // assert file was created
        $this->assertFileExists('storage/app/exports/'.$this->tableName.'.csv', "Generated CSV does not exist");

        var_dump($this->tableName);

        // delete generated file
        //\Storage::delete('storage/app/exports/'.$this->tableName.'.csv');
        \Storage::delete('/exports/'.$this->tableName.'.csv');        
    }  
    
    // /** @test */
    public function try_to_export_on_nonexisting_table() {
        $this->artisan('table:export', ['tablename' => $this->tableName, '--field' => ['id']])
             ->expectsOutput('Table Doesn\'t Exist.');     
    }     

  }
?>
