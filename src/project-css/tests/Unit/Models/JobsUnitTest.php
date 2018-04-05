<?php
  # app/tests/Unit/controllers/TableControllerUnitTest.php

  use App\Http\Controllers\JobsController;
  use App\Http\Controllers\TableController;
  use App\Jobs\FileImport;
  use App\Models\Jobs;

  class JobsControllerUnitTest extends TestCase {

    public function setUp() {
         parent::setUp();
         Artisan::call('migrate');
         Artisan::call('db:seed');
    }

    protected function tearDown() {
      Artisan::call('migrate:reset');
      parent::tearDown();
    }

    public function testFailedJobs() {
      $this->assertEquals(0, (new Jobs())->getFailedJobsCount());
    }

    public function testPendingJobs() {
      $this->assertEquals(0, (new Jobs())->getPendingJobsCount());
    }

  }
?>
