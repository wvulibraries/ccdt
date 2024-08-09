<?php
  # app/tests/Unit/Console/renameCollectionConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class RenameCollectionTest extends TestCase
  {
    private $colName;
    private $colName2;
    private $helper;    

    public function setUp(): void {
      parent::setUp();

      $this->helper = new CollectionHelper; 

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();
      $this->colName2 = $this->colName . '1';  
    } 
    
    protected function tearDown(): void {
        // Delete Test Collections
        $this->testHelper->deleteTestCollections();   
        parent::tearDown();
    }    

    /** @test */
    public function it_has_collection_rename_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\RenameCollection::class));
    }

    /** @test */
    public function it_renames_a_collection() {
        Artisan::call('collection:create', ['collectioname' => $this->colName]);

        $this->artisan('collection:rename', ['collectioname' => $this->colName, 'newname' => $this->colName2] )
             ->expectsOutput('Collection Has been Renamed.'); 
    } 

    /** @test */
    public function try_to_rename_a_collection_with_existing_name() {
        Artisan::call('collection:create', ['collectioname' => $this->colName]);
        Artisan::call('collection:create', ['collectioname' => $this->colName2]);

        $this->assertEquals(Collection::all()->count(), 2);

        $this->artisan('collection:rename', ['collectioname' => $this->colName2, 'newname' => $this->colName] )
             ->expectsOutput('Collection ' . $this->colName . ' Already Exists');            
    }      

    /** @test */
    public function try_to_rename_a_nonexisting_collection() {
        $this->artisan('collection:rename', ['collectioname' => $this->colName, 'newname' => $this->colName2] )
             ->expectsOutput('Collection ' . $this->colName . ' Doesn\'t Exist');     
    }      

  }
?>
