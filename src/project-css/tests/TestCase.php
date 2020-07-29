<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';
    
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

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication() {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        $this->clearCache();
        return $app;
    }
}
