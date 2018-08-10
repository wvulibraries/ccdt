<?php
    # app/tests/controllers/UploadControllerTest.php

    use Illuminate\Support\Facades\Storage;
    use App\Models\Jobs;
    use App\Models\User;

    class JobsControllerTest extends BrowserKitTestCase {

    private $admin;
    private $user;

    // use the factory to create a Faker\Generator instance
    public $faker;

    public function setUp() {
        parent::setUp();
        Artisan::call('migrate');
        Artisan::call('db:seed');

        // find admin and test user accounts
        $this->admin = User::where('name', '=', 'admin')->first();
        $this->user = User::where('name', '=', 'test')->first();

        $this->faker = Faker\Factory::create();
    }

    protected function tearDown() {
        Artisan::call('migrate:reset');
        parent::tearDown();
    }

    public function testViewPendingJobs() {
        $this->actingAs($this->admin)
             ->visit('admin/jobs/pending')
             ->see('No Pending Job(s).')
             ->assertResponseStatus(200);

        // standard user should be redirect to the dashboard
        $this->actingAs($this->user)
             ->visit('admin/jobs/pending')
             ->see('Dashboard')
             ->assertResponseStatus(200);
    }

    public function testViewFailedJobs() {
        $this->actingAs($this->admin)
             ->visit('admin/jobs/failed')
             ->see('No Failed Job(s).')
             ->assertResponseStatus(200);

        // standard user should be redirect to the dashboard
        $this->actingAs($this->user)
             ->visit('admin/jobs/failed')
             ->see('Dashboard')
             ->assertResponseStatus(200);
    }

    public function testRetryFailedJob() {
        \DB::insert('insert into failed_jobs (payload) values(?)',[$this->faker->text]);
        $this->actingAs($this->admin)
             ->visit('admin/jobs/retry/1')
             ->see('No Failed Job(s).')
             ->assertResponseStatus(200);
    }

    public function testRetryAllFailedJobs() {
        \DB::insert('insert into failed_jobs (payload) values(?)',[$this->faker->text]);
        $this->actingAs($this->admin)
             ->visit('admin/jobs/retryall')
             ->see('No Failed Job(s).')
             ->assertResponseStatus(200);
    }

    public function testForgetJob() {
        \DB::insert('insert into failed_jobs (payload) values(?)',[$this->faker->text]);
        $this->actingAs($this->admin)
             ->visit('admin/jobs/forget/1')
             ->see('No Failed Job(s).')
             ->assertResponseStatus(200);
    }

    public function testForgetAllFailedJobs() {
        \DB::insert('insert into failed_jobs (payload) values(?)',[$this->faker->text]);
        $this->actingAs($this->admin)
             ->visit('admin/jobs/flush')
             ->see('No Failed Job(s).')
             ->assertResponseStatus(200);
    }
  }
?>
