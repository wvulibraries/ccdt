<?php
  # app/tests/controllers/UserControllerTest.php

  use App\Models\User;

  class UserControllerTest extends BrowserKitTestCase {

    private $admin;
    private $user;

    public function setUp(): void {
         parent::setUp();

         // find admin and test user accounts
         $this->admin = User::where('name', '=', 'admin')->first();
         $this->user = User::where('name', '=', 'test')->first();
    }

    public function testDatabase()
    {
         // test database by checking if our user is in the users table
         $this->seeInDatabase('users', [ 'id' => $this->admin->id ]);
         $this->seeInDatabase('users', [ 'id' => $this->user->id ]);
    }

    public function testUserIndexView() {
         $this->actingAs($this->admin)
              ->get('/users')
              ->assertResponseStatus(200);
    }

    public function testNonAdminCannotManageUsers() {
        // try to get to the user(s) page
         $this->actingAs($this->user)
              ->get('users')
              //invalid user gets redirected
              ->assertResponseStatus(302);
    }

    public function testAdminCanManageUsers() {
         // try to get to the user(s) page
         $this->actingAs($this->admin)
              ->get('users')
              //valid admin user should get response 200
              ->assertResponseStatus(200);
    }

    public function testAdminCanPromoteUsers() {
        // While using a admin account try to promote non-admin user
        $this->actingAs($this->admin)
             ->withoutMiddleware()
             ->post('user/promote', [ 'userPromoteId' => $this->user->id, 'name' => $this->user->name ]);

        // check if user was promoted to admin
        $user = User::find($this->user->id);
        $this->assertTrue((boolean) $user->isAdmin);        
    }

    public function testChangingUserToAdminAndBack() {
        //While using a admin account try to promote non-admin user
        $this->actingAs($this->admin)
             ->withoutMiddleware()
             ->post('user/promote', [ 'userPromoteId' => $this->user->id, 'name' => $this->user->name ]);

        //check if user was promoted to admin
        $user = User::find($this->user->id);
        $this->assertTrue((boolean) $user->isAdmin);

        // While using a admin account try to demote admin user previously promoted
        $this->actingAs($this->admin)
             ->withoutMiddleware()
             ->post('user/demote', [ 'userDemoteId' => $user->id, 'name' => $user->name ]);

        //check if user was demoted to user
        $user = User::find($this->user->id);
        $this->assertFalse((boolean) $user->isAdmin);        
     }

    public function testFailedPromote() {
        // While using a admin account try to promote a unknown user
        $this->actingAs($this->admin)
             ->withoutMiddleware()
             ->post('user/promote', [ 'userPromoteId' => 3, 'name' => $this->user->name ])
             ->assertResponseStatus(404);
    }

    public function testIncorrectNamePromote() {
        // While using a admin account try to promote a user but suppling a incorrect name
        $this->actingAs($this->admin)
             ->withoutMiddleware()        
             ->post('user/promote', [ 'userPromoteId' => $this->user->id, 'name' => 'incorrect name' ])
             ->assertSessionHasErrors();
    }

    public function testIncorrectNameDemote() {
        // While using a admin account try to promote non-admin user
        $this->actingAs($this->admin)
             ->withoutMiddleware()        
             ->post('user/promote', [ 'userPromoteId' => $this->user->id, 'name' => $this->user->name ]);

        //check if user was promoted to admin
        $user = User::find($this->user->id);
        $this->assertTrue((boolean) $user->isAdmin);

        // While using a admin account try to demote user with invalid name
        $this->actingAs($this->admin)
             ->withoutMiddleware()        
             ->post('user/demote', [ 'userDemoteId' => $this->user->id, 'name' => 'invalid name' ])
             ->assertSessionHasErrors();
    }

    public function testIncorrectIdDemote() {
        // While using a admin account try to demote a unknown user
        $this->actingAs($this->admin)
             ->withoutMiddleware()
             ->post('user/demote', [ 'userDemoteId' => 3, 'name' => $this->user->name ])
             ->assertResponseStatus(404);
    }

    public function testRestrictingUserAccessThenGivingItBack() {
        // While using a admin account try to restrict a users access
        $this->actingAs($this->admin)
             ->withoutMiddleware() 
             ->post('user/restrict', [ 'userRestrictId' => $this->user->id, 'name' => $this->user->name ]);

        //check if user access was changed
        $user = User::find($this->user->id);
        $this->assertFalse((boolean) $user->hasAccess);

        // While using a admin account try to allow a users access
        $this->actingAs($this->admin)
             ->withoutMiddleware()        
             ->post('user/allow', [ 'userAllowId' => $this->user->id, 'name' => $this->user->name ]);

        //check if user was enabled to user
        $user = User::find($user->id);
       $this->assertTrue((boolean) $user->hasAccess);
    }

  }
?>
