<?php
  # app/tests/Unit/Console/importTableConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class ImportTableTest extends TestCase
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
    public function it_has_import_table_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\ImportTable::class));
    }

    /** @test */
    public function it_creates_a_table() {
        $path = './storage/app/files/test/';
        $file = 'zillow.csv';
        $storageFolder = 'flatfiles';
        $fltFleAbsPth = './storage/app'.'/'.$storageFolder.'/';
        copy($path.$file, $fltFleAbsPth.$file);

        Artisan::call('table:import', ['collectioname' => $this->colName, 'tablename' => $this->tableName, 'filename' => $file]);

        $this->assertEquals(DB::table('tables')->count(), 1);

        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTablesAndFiles([$file]); 
    }  
    
    /** @test */
    public function it_cannot_create_a_table_with_existing_name() {
        $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

        $path = './storage/app/files/test/';
        $file = 'zillow.csv';
        $storageFolder = 'flatfiles';
        $fltFleAbsPth = './storage/app'.'/'.$storageFolder.'/';
        copy($path.$file, $fltFleAbsPth.$file);

        $this->artisan('table:import', ['collectioname' => $this->colName, 'tablename' => $this->tableName, 'filename' => $file])
             ->expectsOutput('Table Name Already Exists.');  

        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTablesAndFiles([$file]); 
    }    

  }
?>
