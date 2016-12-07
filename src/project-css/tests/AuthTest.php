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
      // Generate a ranom name
      $thisName = str_random(8);
      // Genearte a random email
      $thisEmail = str_random(8)."@google.com";

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
