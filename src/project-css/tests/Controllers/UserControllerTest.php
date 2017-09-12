<?php
  # app/tests/controllers/HomeControllerTest.php

  use App\User;
  use App\Http\Controllers\UserController;

  class UserControllerTest extends TestCase {

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

    public function testUserIndexView(){
        // test index users page while admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();
        $this->actingAs($admin)->get('/users')
                               ->assertResponseStatus(200);
    }

    public function testNonAdminCannotManageUsers(){
        // find non-admin user
        $user = App\User::where('isAdmin', '=', '0')->first();

        // try to get to the user(s) page
        $this->actingAs($user)
            ->get('users')
            //invalid user gets redirected
            ->assertResponseStatus(302);
    }

    public function testAdminCanManageUsers(){
        // find admin user
        $user = App\User::where('isAdmin', '=', '1')->first();

        // try to get to the user(s) page
        $this->actingAs($user)
            ->get('users')
            //valid admin user should get response 200
            ->assertResponseStatus(200);
    }

    public function testChangingUserToAdminAndBack(){
        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();
        // Generate Test User
        $user = factory(App\User::class)->create([
            'name' => $this->userName,
            'email' => $this->userEmail,
            'password' => bcrypt($this->userPass),
        ]);

        // While using a admin account try to promote non-admin user
        $response = $this->actingAs($admin)->post('user/promote', ['id' => $user->id, 'name' => $this->userName]);

        //check if user was promoted to admin
        $user = User::find($user->id);
        $this->assertEquals('1', $user->isAdmin);

        // While using a admin account try to demote admin user previously promoted
        $response = $this->actingAs($admin)->post('user/demote', ['id' => $user->id, 'name' => $this->userName]);

        //check if user was demoted to user
        $user = User::find($user->id);
        $this->assertEquals('0', $user->isAdmin);
    }

    public function testFailedPromote(){
        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();

        // While using a admin account try to promote a unknown user
        $this->actingAs($admin)->post('user/promote', ['id' => 3, 'name' => $this->userName])
                               ->assertResponseStatus(404);
    }

    public function testIncorrectNamePromote(){
        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();

        // Generate Test User
        $user = factory(App\User::class)->create([
            'name' => $this->userName,
            'email' => $this->userEmail,
            'password' => bcrypt($this->userPass),
        ]);

        // While using a admin account try to promote a user but suppling a incorrect name
        $response = $this->actingAs($admin)->post('user/promote', ['id' => $user->id, 'name' => 'incorrect name']);

        $this->assertSessionHasErrors();
    }

    public function testIncorrectNameDemote(){
        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();

        // Generate Test User
        $user = factory(App\User::class)->create([
            'name' => $this->userName,
            'email' => $this->userEmail,
            'password' => bcrypt($this->userPass),
        ]);

        // While using a admin account try to promote non-admin user
        $response = $this->actingAs($admin)->post('user/promote', ['id' => $user->id, 'name' => $this->userName]);

        //check if user was promoted to admin
        $user = User::find($user->id);
        $this->assertEquals('1', $user->isAdmin);

        // While using a admin account try to demote user with invalid name
        $response = $this->actingAs($admin)->post('user/demote', ['id' => $user->id, 'name' => 'invalid name']);

        $this->assertSessionHasErrors();
    }

    public function testIncorrectIdDemote(){
        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();

        // While using a admin account try to demote a unknown user
        $this->actingAs($admin)->post('user/demote', ['id' => 3, 'name' => $this->userName])
                               ->assertResponseStatus(404);
    }

    public function testRestrictingUserAccessThenGivingItBack(){
        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();
        // Generate Test User
        $user = factory(App\User::class)->create([
            'name' => $this->userName,
            'email' => $this->userEmail,
            'password' => bcrypt($this->userPass),
        ]);

        // While using a admin account try to restrict a users access
        $response = $this->actingAs($admin)->post('user/restrict', ['id' => $user->id, 'name' => $this->userName]);

        //check if user access was changed
        $user = User::find($user->id);
        $this->assertEquals('0', $user->hasAccess);

        // While using a admin account try to allow a users access
        $response = $this->actingAs($admin)->post('user/allow', ['id' => $user->id, 'name' => $this->userName]);

        //check if user was enabled to user
        $user = User::find($user->id);
        $this->assertEquals('1', $user->hasAccess);
    }

  }
?>
