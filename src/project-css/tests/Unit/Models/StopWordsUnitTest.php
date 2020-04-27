<?php
  # app/tests/Unit/controllers/StopWordsUnitTest.php

  use App\Models\StopWords;

  class StopWordsUnitTest extends BrowserKitTestCase
  {
       
    public function testIsStopWord() {
         //verify adam is not a stop word should return false
         $this->assertTrue(StopWords::isStopWord('no'));
         $this->assertFalse(StopWords::isStopWord('adam'));
    }

  }
?>
