<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase
{
    /**
     * Check for the registration form
     *
     * @return void
     */
    public function testNewUserRegister(){
      // Go to login page and enter credentials
      //credentials
      $adminEmail = "admin@admin.com";
      $adminPass = "testing";

      // Generate a ranom name
      $thisName = str_random(8);
      // Genearte a random email
      $thisEmail = str_random(8)."@google.com";

      // Type some valid values
      $this->visit('/login')
           ->type($adminEmail,'email')
           ->type($adminPass,'password')
           ->press('Login');


      // Type some valid values
      $this->visit('/register')
           ->type($thisName,'name')
           ->type($thisEmail,'email')
           ->type('password123','password')
           ->type('password123','password_confirmation')
           ->press('Register')
           ->seePageIs('/home');
    }
}
