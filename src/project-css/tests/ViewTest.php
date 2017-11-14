<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewTest extends TestCase
{

    private $adminEmail;
    private $adminPass;
    private $userName;
    private $userEmail;
    private $userPass;

    public function setUp() {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        //admin credentials
        $this->adminEmail = "admin@admin.com";
        $this->adminPass = "testing";

        //user credentials
        $this->userName = "testuser";
        $this->userEmail = "testuser@google.com";
        $this->userPass = "testing";
    }

    protected function tearDown() {
        Artisan::call('migrate:reset');
        parent::tearDown(); // Moving that call to the top of the function didn't work either.
    }

    /**
     * Check the views for the home page
     *
     * @return void
     */
    public function testWelcomeView()
    {
        // Check for the title of home page
        $this->visit('/')->see('Constituent Correspondence Data Tool');

        // Check for the links
        $this->visit('/')->click('CCDT')->seePageIs('/');
        $this->visit('/')->click('Login')->seePageIs('/login');
    }

    /**
     * Check the views for the admin page on logging
     * into the view
     *
     * @return void
     */
    public function testAdminView()
    {
        // Go to login page and enter credentials
        // Type some valid values
        $this->visit('/login')
             ->type($this->adminEmail,'email')
             ->type($this->adminPass,'password')
             ->press('Login')
             ->seePageIs('/home')
             ->see('Dashboard');

        //Check for the links with in the page
        $this->visit('/home')->click('Dashboard')->seePageIs('/home');
        // $this->visit('/home')->click('Create Collection')->seePageIs('/collection/create');
    }

    public function testUserIndex() {
        // Go to login page and enter credentials
        $this->visit('/login')
             ->type($this->adminEmail, 'email')
             ->type($this->adminPass, 'password')
             ->press('Login')
             ->seePageIs('/home');

        // Visit User(s) section
        $this->visit('/users')
             ->see('Create, view and manage users here.');
    }

    public function testUserRegister() {
        // Go to login page and enter credentials
        $this->visit('/login')
             ->type($this->adminEmail, 'email')
             ->type($this->adminPass, 'password')
             ->press('Login')
             ->seePageIs('/home');

        // Go to register and create new user
       $this->visit('/register')
            ->type($this->userName, 'name')
            ->type($this->userEmail, 'email')
            ->type($this->userPass, 'password')
            ->type($this->userPass, 'password_confirmation')
            ->press('Register')
            ->see('Create, view and manage users here.')
            ->see($this->userName);
    }

    /**
     * Check the views for the admin page on logging
     * into the view
     *
     * @return void
     */
     public function testDashboardViews() {
         // Click on the all the links on dashboard
         // Type some valid values
         $this->visit('/login')
              ->type($this->adminEmail, 'email')
              ->type($this->adminPass, 'password')
              ->press('Login')
              ->seePageIs('/home')
              ->see('Dashboard');

          // Check for the dashboard views
          $this->visit('/home')
               ->click('User(s)')
               ->see('Create User(s)');

          // Check Create Collections(s)
          $this->visit('/home')
               ->click('Collection(s)')
               ->see('Create Collection(s)');

          // Check for Load Data
          $this->visit('/home')
               ->click('Table(s)')
               ->see('Create Table(s)')
               ->see('Load Data');

          // Check for the dashboard views
          $this->visit('/help')
               ->see('Help')
               ->see('Search');
     }
}
