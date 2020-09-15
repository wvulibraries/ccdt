<?php
  # app/tests/Unit/Console/deleteCollectionConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class DeleteCollectionConsoleTest extends TestCase
  {
    private $colName; 
    private $helper;
    private $tableName;   

    public function setUp(): void {
      parent::setUp();

      $this->helper = new CollectionHelper; 

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName(); 
    } 
    
    protected function tearDown(): void {
        // Delete Test Collections
        $this->testHelper->deleteTestCollections();   
        parent::tearDown();
    }    

    /** @test */
    public function it_has_collection_delete_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\DeleteCollection::class));
    }

    /** @test */
    public function it_deletes_a_collection() {
        // Create Collection
        Artisan::call('collection:create', ['collectioname' => $this->colName]);
          
        $this->artisan('collection:delete', ['collectioname' => $this->colName] )
             ->expectsOutput('Collection Has been Deleted.');
    }        

    /** @test */
    public function try_to_delete_a_missing_collection() {    
        $this->artisan('collection:delete', ['collectioname' => $this->colName] )
             ->expectsOutput('Collection ' . $this->colName . ' Doesn\'t Exist');         
    }   

    /** @test */
    public function try_to_delete_a_collection_with_table() {    
        // Generate Table Name
        $tblNme = $this->testHelper->createTableName(); 
        $this->testHelper->createCollectionWithTable($this->colName, $tblNme);
        $this->artisan('collection:delete', ['collectioname' => $this->colName] )
             ->expectsOutput('Unable to remove Collection ' . $this->colName . '. Tables are Associated With the Collection.');         
    } 

    /** @test */    
    public function try_to_delete_a_collection_with_files() {
        // Create Collection
        Artisan::call('collection:create', ['collectioname' => $this->colName]);      
      
        // passing a empty file should throw an exception
        $path = './storage/app';
        $file = 'empty.csv';

        // create file in collection storage folder
        $emptyFile = $path.'/'.$this->colName.'/'.$file;
        touch($emptyFile);

        $this->artisan('collection:delete', ['collectioname' => $this->colName] )
             ->expectsOutput('Unable to remove Collection ' . $this->colName . '. Files Exist in Storage Folder.');         
    } 

  }
?>
