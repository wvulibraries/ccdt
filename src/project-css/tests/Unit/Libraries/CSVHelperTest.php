<?php

use App\Libraries\CSVHelper;

class CSVHelperTest extends TestCase
{
    public function setUp() {
         parent::setUp();
    }

    protected function tearDown() {
         parent::tearDown();
    }

    public function testgetTypesFromCSVwithmlbplayers() {
      // test should return an array of vaild types for the database table
      // creation. Pass boolean for it to read header row, filename and maximum
      // number of records to read to determine field types

      //pass true if contains header row, file location, number of rows to check
      $result = (new CSVHelper)->determineTypes(true, '/files/test/mlb_players.csv', 1000);

      // array should have a count of 6 items
      $this->assertEquals(count($result), 6);

      // expected results
      $this->assertEquals($result[0][0], 'string');
      $this->assertEquals($result[0][1], 'default');

      $this->assertEquals($result[1][0], 'string');
      $this->assertEquals($result[1][1], 'default');

      $this->assertEquals($result[2][0], 'string');
      $this->assertEquals($result[2][1], 'default');

      $this->assertEquals($result[3][0], 'integer');
      $this->assertEquals($result[3][1], 'default');

      $this->assertEquals($result[4][0], 'integer');
      $this->assertEquals($result[4][1], 'default');

      $this->assertEquals($result[5][0], 'integer');
      $this->assertEquals($result[5][1], 'default');
    }

    public function testgetTypesFromCSVwithHeaderZillow() {
      // test should return an array of vaild types for the database table
      // creation. Pass boolean for it to read header row, filename and maximum
      // number of records to read to determine field types

      //pass true if contains header row, file location, number of rows to check
      $result = (new CSVHelper)->determineTypes(true, '/files/test/zillow.csv', 10);

      // array should have a count of 7 items
      $this->assertEquals(count($result), 7);

      // expected results
      $this->assertEquals($result[0][0], 'integer');
      $this->assertEquals($result[0][1], 'default');

      $this->assertEquals($result[1][0], 'integer');
      $this->assertEquals($result[1][1], 'default');

      $this->assertEquals($result[2][0], 'integer');
      $this->assertEquals($result[2][1], 'default');

      $this->assertEquals($result[3][0], 'integer');
      $this->assertEquals($result[3][1], 'default');

      $this->assertEquals($result[4][0], 'integer');
      $this->assertEquals($result[4][1], 'default');

      $this->assertEquals($result[5][0], 'integer');
      $this->assertEquals($result[5][1], 'default');

      $this->assertEquals($result[6][0], 'integer');
      $this->assertEquals($result[6][1], 'default');
    }

}
