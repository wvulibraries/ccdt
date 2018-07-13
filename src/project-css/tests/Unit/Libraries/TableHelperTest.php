<?php
use App\Libraries\CSVHelper;
use App\Libraries\TableHelper;

class TableHelperTest extends TestCase
{
    public function setUp() {
         parent::setUp();
         // create cms collection for testing

    }

    protected function tearDown() {
         parent::tearDown();
    }

    // public function testCreateTable() {
    //   // set location of file
    //   $filename = '/files/test/8E.tab';
    //   //get types for table
    //   $fieldTypes = (new CSVHelper)->determineTypes(false, $filename, 100);
    //   $this->assertFalse(strpos($fieldTypes, 'testing'));
    // }

}
