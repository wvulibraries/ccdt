<?php
  # app/tests/controllers/CollectionControllerTest.php

  use App\Http\Controllers\CollectionController;

  class CollectionControllerTest extends TestCase {

    private $adminEmail;
    private $adminPass;
    private $userName;
    private $userEmail;
    private $userPass;

    public function setUp(){
      parent::setUp();
      Artisan::call('migrate');
      Artisan::call('db:seed');

      //admin credentials
      $this->adminEmail = "admin@admin.com";
      $this->adminPass = "testing";

      //user credentials
      $this->userName = "testuser";
      $this->userEmail = "testuser@google.com";
      $this->userPass = "testing";
    }

    protected function tearDown() {
      Artisan::call('migrate:reset');
      parent::tearDown();
    }

    public function testCreateCollection(){
      // Go to login page and enter credentials
      $this->visit('/login')
           ->type($this->adminEmail,'email')
           ->type($this->adminPass,'password')
           ->press('Login')
           ->seePageIs('/home');

      // Go to collection and create new collection
      $this->visit('/collection')
           ->see('Collection Name')
           ->type('collection1','clctnName')
           ->press('Create')
           ->see('Create, import and manage collections here.')
           ->see('collection1');
    }

    public function testNonAdminCannotCreateCollection(){
        // find non-admin user
        $user = App\User::where('isAdmin', '=', '0')->first();

        // try to get to the user(s) page
        $this->actingAs($user)
            ->get('collection')
            //invalid user gets redirected
            ->assertResponseStatus(302);
    }

    // public function testAdminCanCreateCollection(){
    //     // find admin user
    //     $admin = App\User::where('isAdmin', '=', '1')->first();
    //
    //     // try to get to the user(s) page
    //     $this->actingAs($admin)
    //         ->get('collection')
    //         //valid admin user should get response 200
    //         ->assertResponseStatus(200);
    // }

    // public function testAdminCreateCollection(){
    //     // find admin user
    //     $admin = App\User::where('isAdmin', '=', '1')->first();
    //
    //     // try to get to the user(s) page
    //     $this->actingAs($admin)
    //         ->get('table/create')
    //         ->see('Create, import and manage collections here.')
    //         //valid admin user should get response 200
    //         ->assertResponseStatus(200);
    // }

    public function testEditingCollectionName(){
        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();

        // Generate Test Collection
        $collection = factory(App\Collection::class)->create([
            'clctnName' => 'collection1',
        ]);

        // While using a admin account try to rename collection name
        $this->actingAs($admin)
             ->post('collection/edit', ['id' => $collection->id, 'clctnName' => 'collection2']);

        //check if collection was renamed
        $collection = App\Collection::find($collection->id);
        $this->assertEquals('collection2', $collection->clctnName);
    }

    public function testDisableThenEnableCollection(){
        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();

        // Generate Test Collection
        $collection = factory(App\Collection::class)->create([
            'clctnName' => 'collection1',
        ]);

        // While using a admin account try to disable a collection with invalid name (should be redirected)
        $this->actingAs($admin)->post('collection/disable', ['id' => $collection->id, 'clctnName' => 'collection'])->assertResponseStatus(302);

        // While using a admin account try to disable a collection
        $this->actingAs($admin)->post('collection/disable', ['id' => $collection->id, 'clctnName' => $collection->clctnName]);

        // Verify Collection is disabled
        $collection = App\Collection::find($collection->id);
        $this->assertEquals('0', $collection->isEnabled);

        // While using a admin account try to enable a collection with invalid name (should be redirected)
        $this->actingAs($admin)->post('collection/enable', ['id' => $collection->id, 'clctnName' => 'collection'])->assertResponseStatus(302);

        // While using a admin account try to enable a collection
        $this->actingAs($admin)->post('collection/enable', ['id' => $collection->id]);

        $collection = App\Collection::find($collection->id);
        $this->assertEquals('1', $collection->hasAccess);
    }

    public function testNonAdminDisableCollection(){
        // find user user
        $user = App\User::where('isAdmin', '=', '0')->first();

        // Generate Test Collection
        $collection = factory(App\Collection::class)->create([
            'clctnName' => 'collection1',
        ]);

        // While using a admin account try to disable a collection
        $this->actingAs($user)->post('collection/disable', ['id' => $collection->id, 'clctnName' => $collection->clctnName]);

        // Verify Collection hasn't changed
        $collection = App\Collection::find($collection->id);
        $this->assertEquals('1', $collection->isEnabled);
    }


  }
?>
