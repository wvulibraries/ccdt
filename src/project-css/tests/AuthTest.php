<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    protected function setUp() {
      //credentials
      $adminEmail = "admin@admin.com";
      $adminPass = "testing";

      // Generate a ranom name
      $thisName = str_random(8);
      // Genearte a random email
      $thisEmail = str_random(8)."@google.com";
      $thisPass = 'password123';
    }

    protected function tearDown() {
        // delete test user
        $user = User::find($thisEmail)
    }

    /**
     * Check for the registration form
     *
     * @return void
     */
    public function testNewUserRegister(){
      // Go to login page and enter credentials

      // Type some valid values
      $this->visit('/login')
           ->type($adminEmail,'email')
           ->type($adminPass,'password')
           ->press('Login');

      // Type some valid values
      $this->visit('/register')
           ->type($thisName,'name')
           ->type($thisEmail,'email')
           ->type($thisPass,'password')
           ->type($thisPass,'password_confirmation')
           ->press('Register')
           ->seePageIs('/users');
    }

    public function testLoginNewUser() {
      // Type some valid values
      $this->visit('/login')
           ->type($thisName,'email')
           ->type($thisPass,'password')
           ->press('Login')
           ->seePageIs('/home');
    }
}
