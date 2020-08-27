<?php
  # app/tests/Unit/Console/disableCollectionConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class disableCollectionConsoleTest extends TestCase
  {
    private $colName;     

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
    public function it_has_disable_collection_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\DisableCollection::class));
    }

    /** @test */
    public function it_disables_a_collection() {
        // Create Collection
        Artisan::call('collection:create', ['collectioname' => $this->colName]);

        // Disable Collection
        $this->artisan('collection:disable', ['collectioname' => $this->colName] )
             ->expectsOutput('Collection Has been Disabled.'); 

        // find the collection
        $thisClctn = Collection::where('clctnName', $this->colName)->first();      

        // Verify that collection is disabled
        $this->assertEquals(0, $thisClctn->isEnabled);      
    }       
    
    /** @test */
    public function try_to_disable_a_missing_collection() {
        $this->artisan('collection:disable', ['collectioname' => $this->colName] )
             ->expectsOutput('Collection ' . $this->colName . ' Doesn\'t Exist');     
    }

  }
?>
