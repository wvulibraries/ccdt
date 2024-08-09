<?php
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
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();
        $this->clearCache();
        return $app;
    }

    public function setUp(): void {
        parent::setUp();
        Artisan::call('migrate:refresh --seed');  

        // Add testHelper
        $this->testHelper = new TestHelper;    
    }

    protected function tearDown(): void {
        Artisan::call('migrate:rollback');
        parent::tearDown();
    }  
 
}
