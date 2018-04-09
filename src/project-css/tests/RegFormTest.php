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
class RegFormTest extends BrowserKitTestCase
{
    public function setUp() {
      parent::setUp();
      Artisan::call('migrate');
      Artisan::call('db:seed');
    }

    protected function tearDown() {
      Artisan::call('migrate:reset');
      parent::tearDown();
    }

    /**
     * Testing for the password in registration form
     *
     * @return void
     */
    public function testPassword() {
      // Go to login page and enter credentials
      //credentials
      $adminEmail = "admin@admin.com";
      $adminPass = "testing";
      // Type some valid values
      $this->visit('/login')
           ->type($adminEmail, 'email')
           ->type($adminPass, 'password')
           ->press('Login');

      // Test for the name field
      $this->visit('/register')
           ->type('Tyrion Lannister', 'name')
           ->type('user@google.com', 'email')
           ->type('test', 'password')
           ->type('test', 'password_confirmation')
           ->press('Register')
           ->see('The password must be at least 6 characters.');
    }
}
