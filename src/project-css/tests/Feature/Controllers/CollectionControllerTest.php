<?php
  # app/tests/controllers/CollectionControllerTest.php

  use App\Models\User;
  use App\Models\Collection;
  use App\Models\Table;
  use App\Libraries\TestHelper;

  class CollectionControllerTest extends BrowserKitTestCase {

    private $adminEmail;
    private $adminPass;
    private $admin;
    private $user;

    public function setUp(): void {
      parent::setUp();
      Artisan::call('migrate:refresh --seed');

      //admin credentials
      $this->adminEmail = "admin@admin.com";
      $this->adminPass = "testing";

      // find admin and test user accounts
      $this->admin = User::where('name', '=', 'admin')->first();
      $this->user = User::where('name', '=', 'test')->first();
    }

    // protected function tearDown(): void {
    //   Artisan::call('migrate:reset');
    //   parent::tearDown();
    // }

    public function testCreateCollection() {
      // Go to login page and enter credentials
      $this->visit('/login')
           ->type($this->adminEmail, 'email')
           ->type($this->adminPass, 'password')
           ->press('Login')
           ->seePageIs('/home');

      // Go to collection and create new collection
      $this->visit('/collection')
           ->see('Collection Name')
           ->type('collection1', 'clctnName')
           ->press('Create')
           ->see('Create, import and manage collections here.')
           ->see('collection1');
    }

    public function testNonAdminCannotCreateCollection() {
        // try to get to the user(s) page
        $this->actingAs($this->user)
              ->get('collection')
              //invalid user gets redirected
              ->assertResponseStatus(302);
    }

    public function testAdminCanCreateCollection() {
        // try to get to the user(s) page
        $this->actingAs($this->admin)
            ->get('collection')
            //valid admin user should get response 200
            ->assertResponseStatus(200);
    }

    public function testAdminCreateCollection() {
        // try to get to the user(s) page
        $this->actingAs($this->admin)
            ->get('table/create')
            ->see('Create, import and manage collections here.')
            //valid admin user should get response 200
            ->assertResponseStatus(200);
    }

    public function testEditingCollectionName() {
        // Generate Test Collection
        $collection = (new TestHelper)->createCollection('collection1');

        // While using a admin account try to rename collection name
        $this->actingAs($this->admin)
             ->withoutMiddleware()
             ->post('collection/edit', [ 'id' => $collection->id, 'clctnName' => 'collection2' ]);

        //check if collection was renamed
        $collection = Collection::find($collection->id);
        $this->assertEquals('collection2', $collection->clctnName);
    }

    public function testDisableThenEnableCollection() {
        // Generate Test Collection
        $collection = (new TestHelper)->createCollection('collection1');

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

//     public function testNonAdminDisableCollection() {
//         // Generate Test Collection
//         $collection = (new TestHelper)->createCollection('collection1');

//         // While using a admin account try to disable a collection
//         $this->actingAs($this->admin)
//              ->withoutMiddleware()    
//              ->post('collection/disable', [ 'id' => $collection->id, 'clctnName' => $collection->clctnName ]);

//         // Verify Collection hasn't changed
//         $collection = Collection::find($collection->id);
//         $this->assertEquals('1', $collection->isEnabled);
//     }

  }
?>
