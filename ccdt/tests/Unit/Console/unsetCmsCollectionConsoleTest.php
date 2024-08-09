<?php
  # app/tests/Unit/Console/setCmsCollectionConsoleTest.php

  use App\Models\Collection;
  use App\Helpers\CollectionHelper;  

  class unsetCmsCollectionTest extends TestCase
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
    public function it_has_collection_unset_command()
    {
        $this->assertTrue(class_exists(\App\Console\Commands\unsetCmsCollection::class));
    }

    /** @test */
    public function it_unset_cms_on_a_collection() {
        Artisan::call('collection:create', ['collectioname' => $this->colName]);

        $this->assertTrue($this->helper->isCollection($this->colName));

        $this->artisan('collection:cms:unset', ['collectioname' => $this->colName] )
             ->expectsOutput('Collection is set as Flatfile'); 
    }     

    /** @test */
    public function try_to_unset_cms_on_a_nonexisting_collection() {
        $this->artisan('collection:cms:unset', ['collectioname' => $this->colName] )
             ->expectsOutput('Collection ' . $this->colName . ' Doesn\'t Exist');     
    }      

  }
?>
