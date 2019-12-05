<?php
  # app/tests/Unit/Models/JobsUnitTest.php

  use App\Http\Controllers\JobsController;
  use App\Http\Controllers\TableController;
  use App\Jobs\FileImport;
  use App\Models\Jobs;
  use Vendor\fzaninotto\Faker\Generator;

  class JobsUnitTest extends BrowserKitTestCase
  {
    // use the factory to create a Faker\Generator instance
    public $faker;

    public function setUp(): void {
         parent::setUp();
         Artisan::call('migrate:refresh --seed');

         $this->faker = Faker\Factory::create();
    }

    protected function tearDown(): void {
         Artisan::call('migrate:reset');
         parent::tearDown();
    }

    public function testRetryAllFailedJobs() {
         //insert dummy record into the failed jobs
         \DB::insert('insert into failed_jobs (payload) values(?)',[$this->faker->text]);
         //test job exists
         $jobs = new Jobs();
         $this->assertEquals(1, $jobs->getFailedJobsCount());
         $jobs->retryAllFailedJobs();
         $this->assertEquals(0, $jobs->getFailedJobsCount());
    }

    public function testRetryFailedJob() {
         //insert dummy record into the failed jobs
         \DB::insert('insert into failed_jobs (payload) values(?)',[$this->faker->text]);
         //test job exists
         $jobs = new Jobs();
         $this->assertEquals(1, $jobs->getFailedJobsCount());
         $jobs->retryFailedJob(1);
         $this->assertEquals(0, $jobs->getFailedJobsCount());
    }

    public function testForgetAllFailedJobs() {
         //insert dummy record into the failed jobs
         \DB::insert('insert into failed_jobs (payload) values(?)',[$this->faker->text]);
         //test job exists
         $jobs = new Jobs();
         $this->assertEquals(1, $jobs->getFailedJobsCount());
         $jobs->forgetAllFailedJobs();
         $this->assertEquals(0, $jobs->getFailedJobsCount());
    }

    public function testForgetFailedJob() {
         //insert dummy record into the failed jobs
         \DB::insert('insert into failed_jobs (payload) values(?)',[$this->faker->text]);
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
