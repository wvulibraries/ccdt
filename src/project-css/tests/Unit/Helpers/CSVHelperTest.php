<?php

use App\Helpers\CSVHelper;

class CSVHelperTest extends BrowserKitTestCase
{

    public function testSchema() {
        // check for a valid file
        if (File::exists(storage_path('/flatfiles/mlb_players.csv'))) {
          // if a header row exists running schema will return an array
          // containing field names.
          $result = (new CSVHelper)->schema('/files/test/mlb_players.csv');
          $this->assertEquals($result[ 0 ], 'Name');
        }

        // passing a filename that doesn't exits should produce false result
        $this->assertFalse((new CSVHelper)->schema('/files/test/unknown.csv'));

        // passing a file that isn't of the correct type should produce false result
        $this->assertFalse((new CSVHelper)->schema('/files/test/images.png'));

        //passing a empty file should produce a false result
        $emptyFile = './storage/app/files/test/empty.csv';
        touch($emptyFile);
        $this->assertFalse((new CSVHelper)->schema('/files/test/empty.csv'));
        unlink($emptyFile);
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
      $this->assertEquals($result[4][1], 'medium');

      $this->assertEquals($result[5][0], 'integer');
      $this->assertEquals($result[5][1], 'medium');
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
      $this->assertEquals($result[1][1], 'medium');

      $this->assertEquals($result[2][0], 'integer');
      $this->assertEquals($result[2][1], 'default');

      $this->assertEquals($result[3][0], 'integer');
      $this->assertEquals($result[3][1], 'default');

      $this->assertEquals($result[4][0], 'integer');
      $this->assertEquals($result[4][1], 'medium');

      $this->assertEquals($result[5][0], 'integer');
      $this->assertEquals($result[5][1], 'medium');

      $this->assertEquals($result[6][0], 'integer');
      $this->assertEquals($result[6][1], 'medium');
    }

    public function testgetTypesFromCSVwithoutHeader() {
      // test should return an array of vaild types for the database table
      // creation. Pass boolean for it to read header row, filename and maximum
      // number of records to read to determine field types

      //pass true if contains header row, file location, number of rows to check
      $result = (new CSVHelper)->determineTypes(false, '/files/test/1A-random.tab', 10);

      // array should have a count of 7 items
      $this->assertEquals(count($result), 13);

      // expected results
      $this->assertEquals($result[0][0], 'string');
      $this->assertEquals($result[0][1], 'default');

      $this->assertEquals($result[1][0], 'string');
      $this->assertEquals($result[1][1], 'medium');

      $this->assertEquals($result[2][0], 'string');
      $this->assertEquals($result[2][1], 'default');

      $this->assertEquals($result[3][0], 'string');
      $this->assertEquals($result[3][1], 'default');

      $this->assertEquals($result[4][0], 'string');
      $this->assertEquals($result[4][1], 'default');

      $this->assertEquals($result[5][0], 'string');
      $this->assertEquals($result[5][1], 'default');

      $this->assertEquals($result[6][0], 'string');
      $this->assertEquals($result[6][1], 'default');

      $this->assertEquals($result[7][0], 'string');
      $this->assertEquals($result[7][1], 'default');

      $this->assertEquals($result[8][0], 'string');
      $this->assertEquals($result[8][1], 'default');

      $this->assertEquals($result[9][0], 'string');
      $this->assertEquals($result[9][1], 'default');

      $this->assertEquals($result[10][0], 'integer');
      $this->assertEquals($result[10][1], 'big');

      $this->assertEquals($result[11][0], 'string');
      $this->assertEquals($result[11][1], 'default');

      $this->assertEquals($result[12][0], 'string');
      $this->assertEquals($result[12][1], 'default');
    }

    /**
     * test generating header
     * creates random $fieldCount between 5 and 30
     * tests the response from generateHeader
     * counts items and verifys that the first item
     * in the array equals 'Field0'
     */
    public function testgenerateHeader() {
      $fieldCount = rand(5, 30);
      $helper = (new CSVHelper);

      $response = $helper->generateHeader($fieldCount);
      $this->assertEquals(count( (array) $response), $fieldCount);
      $this->assertEquals($response[0], 'Field0');
    }    
}
