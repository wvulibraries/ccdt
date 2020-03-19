<?php
include 'TestHelper.php';

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

abstract class BrowserKitTestCase extends BaseTestCase
{
    use RefreshDatabase;    

    /**
     * The base URL of the application.
     *
     * @var string
     */
    public $baseUrl = 'http://localhost';
    public $testHelper;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        // Add testHelper
        $this->testHelper = new TestHelper;

        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        var_dump(getenv('APP_ENV'));
        var_dump(getenv('DB_CONNECTION'));
        var_dump(getenv('DB_DATABASE'));
        die();

        return $app;
    }

    public function setUp(): void {
        parent::setUp();
        Artisan::call('migrate:refresh --seed');
    }

     protected function tearDown(): void {
          Artisan::call('migrate:rollback');
          parent::tearDown();
     }  
 
}
