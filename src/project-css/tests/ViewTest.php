<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewTest extends TestCase
{
    /**
     * Check the views for the home page
     *
     * @return void
     */
    public function testWelcomeView()
    {
      // Check for the title of home page
      $this->visit('/')->see('Constituent Service System');

      // Check for the links
      $this->visit('/')->click('CSS')->seePageIs('/');
      $this->visit('/')->click('Login')->seePageIs('/login');
      $this->visit('/')->click('Register')->seePageIs('/register');
    }
}
