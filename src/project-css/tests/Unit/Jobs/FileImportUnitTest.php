<?php
  # app/tests/Unit/Jobs/FileImportUnitTest.php

  use App\Jobs\FileImport;

  class FileImportUnitTest extends BrowserKitTestCase {
    // public function setUp(): void {
    //     parent::setUp();
    //     Artisan::call('migrate:fresh --seed');
    // }

    // protected function tearDown(): void {
    //     Artisan::call('migrate:rollback');
    //     parent::tearDown();
    // }  

    public function testPushingFakeJob() {
      // Fake the queue
      Queue::fake();

      // Push a job
      Queue::push(new FileImport('test1', 'flatfile', '2.csv'));

      // Assert the job was pushed to the queue
      Queue::assertPushed(FileImport::class);
    }

  }
?>
