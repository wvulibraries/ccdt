<?php
  # app/tests/Unit/Models/AllowedFileTypesTest.php

  use App\Models\AllowedFileTypes;

  class AllowedFileTypesTest extends BrowserKitTestCase
  {

    public function testIsAllowedType() {
         //verify adam is not a stop word should return false
         $this->assertFalse(AllowedFileTypes::isAllowedType('rime'));
         $this->assertTrue(AllowedFileTypes::isAllowedType('txt'));
    }

  }
?>
