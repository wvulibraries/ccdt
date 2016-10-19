<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HomePageViewTest extends TestCase
{
    /**
     * Testing for the home page view components.
     *
     * @return void
     */
    public function testHeader()
    {
      // Test for the header text
      $this->visit('/')
           ->see('Constituent Service System')
           ->dontsee('Laravel 5');
    }

    /**
     * Testing all the links on home page.
     *
     * @return void
     */
    public function testLinks()
    {
      // Admin Link
      $this->visit('/')
           ->click('Admin')
           ->seePageIs('admin');
    }

}
