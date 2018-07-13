<?php
  # app/tests/Unit/controllers/StopWordsUnitTest.php

  use App\Models\CMSRecords;

  class CMSRecordsUnitTest extends TestCase {

    public function setUp() {
         parent::setUp();
         Artisan::call('migrate');
         Artisan::call('db:seed');
    }

    protected function tearDown() {
         Artisan::call('migrate:reset');
         parent::tearDown();
    }

    public function testIsCMSRecord() {
         $this->assertFalse(CMSRecords::isCMSRecord('A1'));
         //verify 1A is identified as a CMS Record
         $this->assertTrue(CMSRecords::isCMSRecord('1A'));
    }

    public function testgetCMSHeaderFailure() {
        $response = CMSRecords::getCMSHeader('A1');
        $this->assertEquals($response, null);
    }

    public function testgetCMSHeader() {
         //verify 1A is identified as a CMS Record
         $header = array('Record Type', 'Constituent ID', 'Individual Type', 'Prefix', 'First Name', 'Middle Name', 'Last Name', 'Suffix', 'Appellation', 'Salutation', 'Date of Birth', 'No Mail Flag', 'Deceased Flag');
         $response = CMSRecords::getCMSHeader('1A');
         // compare with the $header to verify we have what we expected
         $this->assertEquals($response, $header);
    }

  }
?>
