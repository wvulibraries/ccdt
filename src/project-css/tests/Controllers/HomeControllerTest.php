<?php
  # app/tests/controllers/HomeControllerTest.php

  use App\User;
  use App\Http\Controllers\HomeController;

  class HomeControllerTest extends TestCase {

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
      $this->userEmail = "testuser@google.com";
      $this->userPass = "testing";
    }

    protected function tearDown(){
      Artisan::call('migrate:reset');
      parent::tearDown();
    }

    public function test_login_with_user(){
        // Generate Test User
        $this->user = factory(App\User::class)->create([
            'email' => $this->userEmail,
            'password' => bcrypt($this->userPass),
        ]);

        // Type some valid values
        $this->visit('/login')
             ->type($this->userEmail,'email')
             ->type($this->userPass,'password')
             ->press('Login')
             ->seePageIs('/home')
             ->see('Please kindly select the collection and table to view the records');
    }

    public function test_login_with_admin(){
        // Type some valid values
        $this->visit('/login')
             ->type($this->adminEmail,'email')
             ->type($this->adminPass,'password')
             ->press('Login')
             ->seePageIs('/home')
             ->see('Your data. Your control.')
             ->see('User(s)')
             ->see('Collection(s)')
             ->see('Table(s)')
             ->see('Admin(s)');
    }
  }
?>
