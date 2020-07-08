<?php
  # app/tests/Unit/Jobs/UpdateSearchIndexUnitTest.php

  use App\Jobs\UpdateSearchIndex;

  class UpdateSearchIndexUnitTest extends BrowserKitTestCase {
    public function testPushingFakeJob() {
      // Fake the queue
      Queue::fake();

      // Push a job
      Queue::push(new UpdateSearchIndex('NotATable', []));

      // Assert the job was pushed to the queue
      Queue::assertPushed(UpdateSearchIndex::class);
    }

  }
?>
