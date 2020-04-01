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
     * Clears Laravel Cache.
     */
    protected function clearCache()
    {
        $commands = ['clear-compiled', 'cache:clear', 'view:clear', 'config:clear', 'route:clear'];
        foreach ($commands as $command) {
            \Illuminate\Support\Facades\Artisan::call($command);
        }
    }

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
        $this->clearCache();
        return $app;
    }

    public function setUp(): void {
        parent::setUp();
        // var_dump(env('APP_ENV'));
        // var_dump(env('DB_CONNECTION'));
        // var_dump(env('DB_DATABASE'));
        // var_dump(env('TESTING_DB_CONNECTION'));
        // var_dump(env('TESTING_DB_DATABASE'));
        //die();

        //if ((env('DB_CONNECTION') != NULL) && (env('DB_DATABASE') != NULL)) {
          Artisan::call('migrate:refresh --seed');
        //}   
    }

    protected function tearDown(): void {
        Artisan::call('migrate:rollback');
        parent::tearDown();
    }  
 
}
