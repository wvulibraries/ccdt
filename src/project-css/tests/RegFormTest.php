<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * This class to check for the individual feilds of the
 * registration form.
 *
 *
 */
class RegFormTest extends TestCase
{
    /**
     * Testing for the password in registration form
     *
     * @return void
     */
    public function testPassword(){
      // Test for the name field
      $this->visit('/register')
           ->type('Tyrion Lannister','name')
           ->type('user@google.com','email')
           ->type('test','password')
           ->type('test','password_confirmation')
           ->press('Register')
           ->see('The password must be at least 6 characters.');
    }
}
