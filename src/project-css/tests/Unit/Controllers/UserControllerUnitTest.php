<?php
  # app/tests/Unit/controllers/UserControllerUnitTest.php

  use App\Http\Controllers\UserController;
  use App\Models\User;

  class UserControllerUnitTest extends TestCase
  {
    private $admin;
    private $user;

    public function setUp(): void {
        parent::setUp();

        // find admin and test user accounts
        $this->admin = User::where('name', '=', 'admin')->first();
        $this->user = User::where('name', '=', 'test')->first();            
    }

    // public function testAuthRouteAPI () {
    //   $this->startSession();

    //   $request = new \Illuminate\Http\Request();

    //   // set flatfile name and table name
    //   $request->merge([
    //       'user' => $this->user
    //   ]);       

    //   $response = (new UserController)->AuthRouteAPI($request);

    //   var_dump($response);
    // }

  }
?>
