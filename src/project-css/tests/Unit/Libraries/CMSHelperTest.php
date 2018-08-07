<?php

use App\Libraries\CMSHelper;

class CMSHelperTest extends TestCase
{
  public function setUp() {
       parent::setUp();
       Artisan::call('migrate');
       Artisan::call('db:seed');

       // create a test collection
       \DB::insert('insert into collections (clctnName, isEnabled, hasAccess) values(?, ?, ?)',['collection1', true, true]);
  }

  protected function tearDown() {
       Artisan::call('migrate:reset');
       parent::tearDown();
  }

  public function testgetCMSFields() {
    $fieldType = '1A';
    $helper = (new CMSHelper);

    // by sending 13 as the field count we should get the original header
    $response = $helper->getCMSFields($fieldType, 13);
    $this->assertEquals(count($response), 13);

    $response = $helper->getCMSFields($fieldType, 16);
    $this->assertEquals(count($response), 16);

    // sending an invalid count will not return any results
    $response = $helper->getCMSFields($fieldType, 9);
    $this->assertEquals(count($response), 0);
  }

  public function testgenerateHeader() {
    $fieldCount = rand(5, 30);
    $helper = (new CMSHelper);

    $response = $helper->generateHeader($fieldCount);
    $this->assertEquals(count($response), $fieldCount);
    $this->assertEquals($response[0], 'Field0');
  }

  public function testgetheader() {
    $fieldType = '1A';
    $header = array('Record Type', 'Constituent ID', 'Individual Type', 'Prefix', 'First Name', 'Middle Name', 'Last Name', 'Suffix', 'Appellation', 'Salutation', 'Date of Birth', 'No Mail Flag', 'Deceased Flag');
    $response = (new CMSHelper)->getCMSFields($fieldType, count($header));

    // compare with the $header to verify we have what we expected
    $this->assertEquals($response, $header);
    $this->assertEquals(count($response), count($header));
  }

  public function testcreate1Acmstable() {
    // location of test files
    $filePath = './storage/app';
    $testFilesFolder = 'files/test';

    $thsFltFile = '1A-random.tab';

    $collctnId = 1;
    $tableName = 'collection11A';

    // table should not currently exist
    $this->assertFalse(Schema::hasTable($tableName));

    // pass values to create file
    (new CMSHelper)->createCMSTable($testFilesFolder, $thsFltFile, $collctnId, $tableName);

    // check for Table collection18E
    $this->assertTrue(Schema::hasTable($tableName));

    // check to see if records are loaded
    // get set expected record count and assert they are equal

    // drop test table
    Schema::dropIfExists($tableName);

    // clear folder that was created with the table
    rmdir($filePath.'/'.$tableName);
  }

  public function testcreate1Bcmstable() {
    // location of test files
    $filePath = './storage/app';
    $testFilesFolder = 'files/test';

    $thsFltFile = '1B-random.tab';

    $collctnId = 1;
    $tableName = 'collection11B';

    // table should not currently exist
    $this->assertFalse(Schema::hasTable($tableName));

    // pass values to create file
    (new CMSHelper)->createCMSTable($testFilesFolder, $thsFltFile, $collctnId, $tableName);

    // check for Table collection18E
    $this->assertTrue(Schema::hasTable($tableName));

    // check to see if records are loaded
    // get set expected record count and assert they are equal

    // drop test table
    Schema::dropIfExists($tableName);

    // clear folder that was created with the table
    rmdir($filePath.'/'.$tableName);
  }

  public function testcreate3Dcmstable() {
    // location of test files
    $filePath = './storage/app';
    $testFilesFolder = 'files/test';

    $thsFltFile = '3D-random.tab';

    $collctnId = 1;
    $tableName = 'collection13D';

    // table should not currently exist
    $this->assertFalse(Schema::hasTable($tableName));

    // pass values to create file
    (new CMSHelper)->createCMSTable($testFilesFolder, $thsFltFile, $collctnId, $tableName);

    // check for Table collection18E
    $this->assertTrue(Schema::hasTable($tableName));

    // check to see if records are loaded
    // get set expected record count and assert they are equal

    // drop test table
    Schema::dropIfExists($tableName);

    // clear folder that was created with the table
    rmdir($filePath.'/'.$tableName);
  }

  public function testcreate4Ecmstable() {
    // location of test files
    $filePath = './storage/app';
    $testFilesFolder = 'files/test';

    $thsFltFile = '4E-random.tab';

    $collctnId = 1;
    $tableName = 'collection14E';

    // table should not currently exist
    $this->assertFalse(Schema::hasTable($tableName));

    // pass values to create file
    (new CMSHelper)->createCMSTable($testFilesFolder, $thsFltFile, $collctnId, $tableName);

    // check for Table collection18E
    $this->assertTrue(Schema::hasTable($tableName));

    // check to see if records are loaded
    // get set expected record count and assert they are equal

    // drop test table
    Schema::dropIfExists($tableName);

    // clear folder that was created with the table
    rmdir($filePath.'/'.$tableName);
  }
}
