<?php
  # app/tests/Unit/Console/createCollectionConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class CreateCollectionTest extends TestCase
  {
    private $colName;
    private $helper;    

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
    public function it_has_collection_create_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\CreateCollection::class));
    }

    /** @test */
    public function it_creates_a_collection() {
        $this->artisan('collection:create', ['collectioname' => $this->colName] )
             ->expectsOutput('Collection Has been Created.'); 
    }        

    /** @test */
    public function it_cannot_create_a_existing_collection() {    
        Artisan::call('collection:create', ['collectioname' => $this->colName]);

        // Call helper isCollection Verify Collection Exists
        $this->assertTrue($this->helper->isCollection($this->colName));  
 
        $this->artisan('collection:create', ['collectioname' => $this->colName] )
             ->expectsOutput('Collection ' . $this->colName . ' Already Exists');         
    }

  }
?>
