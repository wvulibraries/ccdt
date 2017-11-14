<?php
  # app/tests/controllers/HomeControllerTest.php

  use App\User;
  use App\Http\Controllers\UserController;

  class UserControllerTest extends TestCase {

    private $admin;
    private $user;

    public function setUp(){
         parent::setUp();
         Artisan::call('migrate');
         Artisan::call('db:seed');

         // find admin and test user accounts
         $this->admin = App\User::where('name', '=', 'admin')->first();
         $this->user = App\User::where('name', '=', 'test')->first();
    }

    protected function tearDown() {
         Artisan::call('migrate:reset');
         parent::tearDown();
    }

    public function testDatabase()
    {
         // test database by checking if our user is in the users table
         $this->seeInDatabase('users', ['id' => $this->admin->id]);
         $this->seeInDatabase('users', ['id' => $this->user->id]);
    }

    public function testUserIndexView(){
         $this->actingAs($this->admin)
              ->get('/users')
              ->assertResponseStatus(200);
    }

    public function testNonAdminCannotManageUsers(){
        // try to get to the user(s) page
         $this->actingAs($this->user)
              ->get('users')
              //invalid user gets redirected
              ->assertResponseStatus(302);
    }

    public function testAdminCanManageUsers(){
         // try to get to the user(s) page
         $this->actingAs($this->admin)
              ->get('users')
              //valid admin user should get response 200
              ->assertResponseStatus(200);
    }

    public function testChangingUserToAdminAndBack(){
        // While using a admin account try to promote non-admin user
        $this->actingAs($this->admin)->post('user/promote', ['id' => $this->user->id, 'name' => $this->user->name]);

        //check if user was promoted to admin
        $user = User::find($this->user->id);
        $this->assertEquals('1', $user->isAdmin);

        // While using a admin account try to demote admin user previously promoted
        $this->actingAs($this->admin)->post('user/demote', ['id' => $user->id, 'name' => $user->name]);

        //check if user was demoted to user
        $user = User::find($this->user->id);
        $this->assertEquals('0', $user->isAdmin);
    }

    public function testFailedPromote(){
        // While using a admin account try to promote a unknown user
        $this->actingAs($this->admin)->post('user/promote', ['id' => 3, 'name' => $this->user->name])
                               ->assertResponseStatus(404);
    }

    public function testIncorrectNamePromote(){
        // While using a admin account try to promote a user but suppling a incorrect name
        $this->actingAs($this->admin)
             ->post('user/promote', ['id' => $this->user->id, 'name' => 'incorrect name'])
             ->assertSessionHasErrors();
    }

    public function testIncorrectNameDemote(){
        // While using a admin account try to promote non-admin user
        $this->actingAs($this->admin)->post('user/promote', ['id' => $this->user->id, 'name' => $this->user->name]);

        //check if user was promoted to admin
        $user = User::find($this->user->id);
        $this->assertEquals('1', $user->isAdmin);

        // While using a admin account try to demote user with invalid name
        $this->actingAs($this->admin)
             ->post('user/demote', ['id' => $this->user->id, 'name' => 'invalid name'])
             ->assertSessionHasErrors();
    }

    public function testIncorrectIdDemote(){
        // While using a admin account try to demote a unknown user
        $this->actingAs($this->admin)->post('user/demote', ['id' => 3, 'name' => $this->user->name])
                               ->assertResponseStatus(404);
    }

    public function testRestrictingUserAccessThenGivingItBack(){
        // While using a admin account try to restrict a users access
        $this->actingAs($this->admin)->post('user/restrict', ['id' => $this->user->id, 'name' => $this->user->name]);

        //check if user access was changed
        $user = User::find($this->user->id);
        $this->assertEquals(false, $user->hasAccess);

        // While using a admin account try to allow a users access
        $this->actingAs($this->admin)->post('user/allow', ['id' => $this->user->id, 'name' => $this->user->name]);

        //check if user was enabled to user
        $user = User::find($user->id);
        $this->assertEquals(true, $user->hasAccess);
    }

  }
?>
