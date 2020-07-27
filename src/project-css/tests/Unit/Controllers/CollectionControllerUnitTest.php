<?php
  # app/tests/Unit/controllers/CollectionControllerUnitTest.php

  use App\Models\User;
  use App\Models\Collection;
  use App\Helpers\CollectionHelper;

  class CollectionControllerUnitTest extends BrowserKitTestCase
  {
    private $colName;
    private $colName2;   

    public function setUp(): void {
      parent::setUp();

      // find admin and test user accounts
      $this->admin = User::where('name', '=', 'admin')->first();
      $this->user = User::where('name', '=', 'test')->first();

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();
      $this->colName2 = $this->colName+1;
    }

    protected function tearDown(): void {
      // test tables, files and folders that were created
      $this->testHelper->cleanupTestTables();

      // Delete Test Collections
      $this->testHelper->deleteTestCollections();         

      parent::tearDown();
    } 

    public function testEditingCollectionName() {
      // Create Test Data Array
      $data = [
            'isCms' => false,
            'name' => $this->colName
      ];

      // Call helper create
      $collection = (new CollectionHelper)->create($data);

      // While using a admin account try to rename collection name
      $this->actingAs($this->admin)
            ->withoutMiddleware()
            ->post('collection/edit', [ 'id' => $collection->id, 'clctnName' => $this->colName2 ]);

      //check if collection was renamed
      $collection = Collection::find($collection->id);
      $this->assertEquals($this->colName2, $collection->clctnName);     
    }

    public function testEditingCollectionIsCms() {
      // Generate Test Collection
      $collection = $this->testHelper->createCollection($this->colName);
      $this->assertEquals($collection->isCms, 0);

      // While using a admin account try to set collection to cms
      $this->actingAs($this->admin)
            ->withoutMiddleware()
            ->post('collection/edit', [ 'id' => $collection->id, 'clctnName' => $this->colName, 'isCms' => 1 ]);

      //check if collection was set to cms
      $collection = Collection::find($collection->id);
      $this->assertEquals($collection->isCms, 1);     
    }    

    public function testDisableThenEnableCollection() {
      // Generate Test Collection
      $collection = $this->testHelper->createCollection($this->colName);

      // While using a admin account try to disable a collection with invalid name (should be redirected)
      $this->actingAs($this->admin)
           ->withoutMiddleware()        
           ->post('collection/disable', [ 'id' => $collection->id, 'clctnName' => 'collection' ])->assertResponseStatus(302);

      // While using a admin account try to disable a collection
      $this->actingAs($this->admin)
           ->withoutMiddleware()    
           ->post('collection/disable', [ 'id' => $collection->id, 'clctnName' => $collection->clctnName ]);

      // Verify Collection is disabled
      $collection = Collection::find($collection->id);
      $this->assertEquals('0', $collection->isEnabled);

      // While using a admin account try to enable a collection with invalid name (should be redirected)
      $this->actingAs($this->admin)
           ->withoutMiddleware()    
           ->post('collection/enable', [ 'id' => $collection->id, 'clctnName' => 'collection' ])->assertResponseStatus(302);

      // While using a admin account try to enable a collection
      $this->actingAs($this->admin)
           ->withoutMiddleware()    
           ->post('collection/enable', [ 'id' => $collection->id ]);

      $collection = Collection::find($collection->id);
      $this->assertEquals('1', $collection->hasAccess);   
    }

    public function testNonAdminDisableCollection() {
      // Generate Test Collection
      $collection = $this->testHelper->createCollection($this->colName);

      // Verify Collection isEnabled
      $collection = Collection::find($collection->id);
      $this->assertEquals('1', $collection->isEnabled);

      // While using a admin account try to disable a collection
      $this->actingAs($this->user)  
           ->post('collection/disable', [ 'id' => $collection->id, 'clctnName' => $collection->clctnName ]);

      // Verify Collection hasn't changed
      $collection = Collection::find($collection->id);
      $this->assertEquals('1', $collection->isEnabled);
    }    

  }
?>
