<?php
  # app/tests/Unit/Models/AllowedFileTypesTest.php

  use App\Models\AllowedFileTypes;

  class AllowedFileTypesTest extends TestCase {

    public function setUp(): void {
         parent::setUp();
         Artisan::call('migrate');
         Artisan::call('db:seed');
    }

    protected function tearDown(): void {
         Artisan::call('migrate:reset');
         parent::tearDown();
    }

    public function testIsAllowedType() {
         //verify adam is not a stop word should return false
         $this->assertFalse(AllowedFileTypes::isAllowedType('rime'));
         $this->assertTrue(AllowedFileTypes::isAllowedType('txt'));
    }

  }
?>
