<?php
  # app/tests/Unit/Models/CMSRecordsUnitTest.php

  use App\Models\CMSRecords;

  class CMSRecordsUnitTest extends BrowserKitTestCase
  {

    public function testIsCMSRecord() {
         $this->assertFalse(CMSRecords::isCMSRecord('A1'));
         //verify 1A is identified as a CMS Record
         $this->assertTrue(CMSRecords::isCMSRecord('1A'));
    }

    public function testgetCMSHeaderFailure() {
        $response = CMSRecords::getCMSHeader('A1');
        // we should have a response count of 0
        $this->assertEquals(0, count($response));
    }

    public function testfindCMSHeader() {
         //verify 1A is identified as a CMS Record
         $header = array('Record Type', 'Constituent ID', 'Individual Type', 'Prefix', 'First Name', 'Middle Name', 'Last Name', 'Suffix', 'Appellation', 'Salutation', 'Date of Birth', 'No Mail Flag', 'Deceased Flag');
         $response = CMSRecords::findCMSHeader('1A', count($header));
         // compare with the $header to verify we have what we expected
         $this->assertEquals(unserialize($response[0]->fieldNames), $header);
    }

    public function testfindCMSHeaderWithId() {
         //verify 1A is identified as a CMS Record
         $header = array('Record Type', 'Constituent ID', 'Individual Type', 'Prefix', 'First Name', 'Middle Name', 'Last Name', 'Suffix', 'Appellation', 'Salutation', 'Date of Birth', 'No Mail Flag', 'Deceased Flag');
         $response = CMSRecords::findCMSHeaderWithId('1A', 1);
         // compare with the $header to verify we have what we expected
         $this->assertEquals(unserialize($response[0]->fieldNames), $header);
    }

    public function testfindClosestCMSHeader() {
        $response = CMSRecords::findClosestCMSHeader('1A', 9);
        // we should have a response count of 1
        $this->assertEquals(2, count($response));
    }

  }
?>
