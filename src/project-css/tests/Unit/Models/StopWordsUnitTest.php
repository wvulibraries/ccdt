<?php
  # app/tests/Unit/controllers/StopWordsUnitTest.php

  use App\Models\StopWords;

  class StopWordsUnitTest extends BrowserKitTestCase
  {

    public function setUp(): void {
         parent::setUp();
         Artisan::call('migrate:refresh --seed');
    }

    protected function tearDown(): void {
         Artisan::call('migrate:reset');
         parent::tearDown();
    }

    public function testIsStopWord() {
         //verify adam is not a stop word should return false
         $this->assertTrue(StopWords::isStopWord('no'));
         $this->assertFalse(StopWords::isStopWord('adam'));
    }

  }
?>
