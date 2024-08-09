<?php
  # app/tests/Unit/Console/enableCollectionConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class enableCollectionConsoleTest extends TestCase
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
    public function it_has_enable_collection_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\EnableCollection::class));
    }

    /** @test */
    public function it_enables_a_collection() {
        // create test collection          
        $this->testHelper->createCollection($this->colName, 0);

        // find the collection
        $thisClctn = Collection::where('clctnName', $this->colName)->first();      

        // Verify that collection is disabled
        $this->assertEquals(0, $thisClctn->isEnabled);       

        $this->artisan('collection:enable', ['collectioname' => $this->colName] )
             ->expectsOutput('Collection Has been Enabled.');    
             
        // find the collection
        $thisClctn = Collection::where('clctnName', $this->colName)->first(); 
        
        // Verify that collection is enabled
        $this->assertEquals(1, $thisClctn->isEnabled);            
    }       
    
    /** @test */
    public function try_to_enable_a_missing_collection() {
        $this->artisan('collection:enable', ['collectioname' => $this->colName] )
             ->expectsOutput('Collection ' . $this->colName . ' Doesn\'t Exist');     
    }      

  }
?>
