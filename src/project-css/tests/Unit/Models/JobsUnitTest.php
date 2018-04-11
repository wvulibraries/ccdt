<?php
  # app/tests/Unit/controllers/TableControllerUnitTest.php

  use App\Http\Controllers\JobsController;
  use App\Http\Controllers\TableController;
  use App\Jobs\FileImport;
  use App\Models\Jobs;

  class JobsUnitTest extends TestCase {

    public function setUp() {
         parent::setUp();
         Artisan::call('migrate');
         Artisan::call('db:seed');
    }

    protected function tearDown() {
         Artisan::call('migrate:reset');
         parent::tearDown();
    }

    public function testRetryAllFailedJobs() {
         //insert dummy record into the failed jobs
         \DB::insert('insert into failed_jobs (payload) values(?)',['test payload']);
         //test job exists
         $jobs = new Jobs();
         $this->assertEquals(1, $jobs->getFailedJobsCount());
         $jobs->retryAllFailedJobs();
         $this->assertEquals(0, $jobs->getFailedJobsCount());
    }

    public function testRetryFailedJob() {
         //insert dummy record into the failed jobs
         \DB::insert('insert into failed_jobs (payload) values(?)',['test payload']);
         //test job exists
         $jobs = new Jobs();
         $this->assertEquals(1, $jobs->getFailedJobsCount());
         $jobs->retryFailedJob(1);
         $this->assertEquals(0, $jobs->getFailedJobsCount());
    }

    public function testForgetAllFailedJobs() {
         //insert dummy record into the failed jobs
         \DB::insert('insert into failed_jobs (payload) values(?)',['test payload']);
         //test job exists
         $jobs = new Jobs();
         $this->assertEquals(1, $jobs->getFailedJobsCount());
         $jobs->forgetAllFailedJobs();
         $this->assertEquals(0, $jobs->getFailedJobsCount());
    }

    public function testForgetFailedJob() {
         //insert dummy record into the failed jobs
         \DB::insert('insert into failed_jobs (payload) values(?)',['test payload']);
         //test job exists
         $jobs = new Jobs();
         $this->assertEquals(1, $jobs->getFailedJobsCount());
         $jobs->forgetFailedJob(1);
         $this->assertEquals(0, $jobs->getFailedJobsCount());
    }

    public function testPendingJobs() {
         //verify queue should be empty
         $this->assertEquals(0, (new Jobs())->getPendingJobsCount());
    }

  }
?>
