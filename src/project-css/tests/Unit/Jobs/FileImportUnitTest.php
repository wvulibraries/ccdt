<?php
  # app/tests/Unit/Jobs/FileImportUnitTest.php

  use App\Jobs\FileImport;

  class FileImportUnitTest extends TestCase {

    public function testPushingFakeJob() {
      // Fake the queue
      Queue::fake();

      // Push a job
      Queue::push(new FileImport('test1', '2.csv'));

      // Assert the job was pushed to the queue
      Queue::assertPushed(FileImport::class);
    }

  }
?>
