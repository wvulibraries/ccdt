<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AuthTest extends TestCase {
    use DatabaseMigrations;

    protected $user;

    private $adminEmail;
    private $adminPass;
    private $userName;
    private $userEmail;
    private $userPass;

    public function setUp() {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        //credentials
        $this->adminEmail = "admin@admin.com";
        $this->adminPass = "testing";

        // Generate a random name
        $this->userName = str_random(8);
        // Genearte a random email
        $this->userEmail = str_random(8)."@google.com";
        $this->userPass = 'password123';
    }

    protected function tearDown() {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }

    /**
     * Check for the registration form
     *
     * @return void
     */
    public function testAdminCreateUser() {
        // Go to login page and enter credentials

        // Type some valid values
        $this->visit('/login')
             ->type($this->adminEmail, 'email')
             ->type($this->adminPass, 'password')
             ->press('Login');

        // Type some valid values
        $this->visit('/register')
             ->type($this->userName, 'name')
             ->type($this->userEmail, 'email')
             ->type($this->userPass, 'password')
             ->type($this->userPass, 'password_confirmation')
             ->press('Register')
             ->seePageIs('/users');
    }

    /** @test */
    public function testWrongLoginCredentials() {
        $this->visit(route('login'))
             ->type($this->userEmail, 'email')
             ->type('invalid-password', 'password')
             ->press('Login')
             ->see('These credentials do not match our records.');
    }

    /** @test */
    public function testForgotPasswordWithIncorrectEmail() {
        $this->visit('/password/reset')
             ->see('Reset Password')
             ->type('test', 'email')
             ->press('Send Password Reset Link')
             ->see('The email must be a valid email address.')
             ->type('test@nowhere.com', 'email')
             ->press('Send Password Reset Link')
             ->see("We can't find a user with that e-mail address.");
    }

    /** @test */
    // fails cannot send email password reset
    // public function testForgotPassword() {
    //     // Generate Test User
    //     $user = factory(App\User::class)->create([
    //         'email' => $this->userEmail,
    //         'password' => bcrypt($this->userPass),
    //     ]);
    //
    //     $response = $this->visit('/password/reset')
    //          ->see('Reset Password')
    //          ->type($this->userEmail, 'email')
    //          ->press('Send Password Reset Link');
    //
    //     var_dump($response);
    // }

    public function testUserRedirectedToDashboard() {
        // Generate Test User
        $user = factory(App\User::class)->create([
            'email' => $this->userEmail,
            'password' => bcrypt($this->userPass),
        ]);

        $this->actingAs($user)
                ->visit(route('login'))
                ->seePageIs(route('home'));
    }

    /** @test */
    public function testUserRedirectedToLoginPage() {
        $this->visit(route('home'));
        $this->seePageIs(route('login'));
    }

}
