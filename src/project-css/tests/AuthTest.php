<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    use DatabaseMigrations;

    protected static $db_inited = false;

    private $adminEmail;
    private $adminPass;
    private $userName;
    private $userEmail;
    private $userPass;

    public function setUp(){
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        //credentials
        $this->adminEmail = "admin@admin.com";
        $this->adminPass = "testing";

        // Generate a ranom name
        $this->userName = str_random(8);
        // Genearte a random email
        $this->userEmail = str_random(8)."@google.com";
        $this->userPass = 'password123';
    }

    // protected function tearDown() {
    //     Artisan::call('migrate:reset');
    //     parent::tearDown();
    //
    //     // delete test user
    //     //$user = App\User::where('email', $this->userEmail)->delete;
    // }

    /**
     * Check for the registration form
     *
     * @return void
     */
    public function testNewUserRegister(){
      // Go to login page and enter credentials

      // Type some valid values
      $this->visit('/login')
           ->type($this->adminEmail,'email')
           ->type($this->adminPass,'password')
           ->press('Login');

      // Type some valid values
      $this->visit('/register')
           ->type($this->userName,'name')
           ->type($this->userEmail,'email')
           ->type($this->userPass,'password')
           ->type($this->userPass,'password_confirmation')
           ->press('Register')
           ->seePageIs('/users');
    }

    public function testLoginNewUser() {
      // Type some valid values
      $this->visit('/login')
           ->type($this->userName,'email')
           ->type($this->userPass,'password')
           ->press('Login');
    }
}
