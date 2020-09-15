<?php

use App\Helpers\CSVHelper;

class CSVHelperTest extends BrowserKitTestCase
{
  private $helper; 

  public function setUp(): void {
    parent::setUp();

    $this->helper = new CSVHelper;
  }

  public function testSchema() {
    $result = $this->helper->schema('/files/test/mlb_players.csv');
    $this->assertEquals($result[ 0 ], 'Name');

    // passing a filename that doesn't exits should produce false result
    $this->assertFalse($this->helper->schema('/files/test/unknown.csv'));

    // passing a file that isn't of the correct type should produce false result
    $this->assertFalse($this->helper->schema('/files/test/images.png'));

    //passing a empty file should produce a false result
    $emptyFile = './storage/app/files/test/empty.csv';
    touch($emptyFile);
    $this->assertFalse($this->helper->schema('/files/test/empty.csv'));
    unlink($emptyFile);
  }

  public function testgetTypesFromCSVwithmlbplayers() {
    // test should return an array of vaild types for the database table
    // creation. Pass boolean for it to read header row, filename and maximum
    // number of records to read to determine field types

    //pass true if contains header row, file location, number of rows to check
    $result = $this->helper->determineTypes(true, '/files/test/mlb_players.csv', 1000);

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
    $this->assertEquals($result[5][1], 'medium');
  }

  public function testgetTypesFromCSVwithHeaderZillow() {
    // test should return an array of vaild types for the database table
    // creation. Pass boolean for it to read header row, filename and maximum
    // number of records to read to determine field types

    //pass true if contains header row, file location, number of rows to check
    $result = $this->helper->determineTypes(true, '/files/test/zillow.csv', 10);

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
    $result = $this->helper->determineTypes(false, '/files/test/1A-random.tab', 10);

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

  public function testgetTypesFromGeneratedCSV() {
    $file = 'testfile2.csv';

    $this->testHelper->createTestCSV($file, 100);

    //pass true if contains header row, file location, number of rows to check
    $result = $this->helper->determineTypes(true, '/flatfiles/'.$file, 10);

    // array should have a count of 9 items
    $this->assertEquals(count($result), 9);

    // expected results
    $this->assertEquals($result[0][0], 'string');
    $this->assertEquals($result[0][1], 'default');

    $this->assertEquals($result[1][0], 'string');
    $this->assertEquals($result[1][1], 'medium');

    $this->assertEquals($result[2][0], 'string');
    $this->assertEquals($result[2][1], 'big');    

    $this->assertEquals($result[3][0], 'text');
    $this->assertEquals($result[3][1], 'default');

    $this->assertEquals($result[4][0], 'text');
    $this->assertEquals($result[4][1], 'medium');
    
    $this->assertEquals($result[5][0], 'text');
    $this->assertEquals($result[5][1], 'big');  
    
    $this->assertEquals($result[6][0], 'integer');
    $this->assertEquals($result[6][1], 'default');

    $this->assertEquals($result[7][0], 'integer');
    $this->assertEquals($result[7][1], 'medium');    
    
    $this->assertEquals($result[8][0], 'integer');
    $this->assertEquals($result[8][1], 'big');  

    // test tables, files and folders that were created
    $this->testHelper->cleanupTestUploads([$file]);    
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

    $response = $this->helper->generateHeader($fieldCount);
    $this->assertEquals(count( (array) $response), $fieldCount);
    $this->assertEquals($response[0], 'Field0');
  }    

  public function testCheckFileMissingFile() {
    //passing a missing file should produce a false result
    $invalidFile = '/files/test/missing.csv';      
    $this->assertFalse($this->helper->checkFile(false, $invalidFile, 10000));
  }    

  public function testCheckFileInvalidMimeType() {
    //passing a invalid file type should produce a false result
    $invalidFile = '/files/test/images.png';      
    $this->assertFalse($this->helper->checkFile(false, $invalidFile, 10000));
  }   

  public function testCheckFileWithEmptyFile() {
    //passing a empty file should produce a false result  
    $emptyFile = 'empty.csv'; 

    touch(\storage_path()."/app/flatfiles/". $emptyFile);
    
    $this->assertFalse($this->helper->checkFile(false, $emptyFile, 10000));

    // test tables, files and folders that were created
    $this->testHelper->cleanupTestUploads([$emptyFile]);     
  }  

  public function testSchemaWithEmptyFile() {
    //passing a empty file should produce a false result  
    $emptyFile = 'empty.csv'; 

    touch(\storage_path()."/app/flatfiles/". $emptyFile);
    
    $this->assertFalse($this->helper->schema($emptyFile));

    // test tables, files and folders that were created
    $this->testHelper->cleanupTestUploads([$emptyFile]);    
  }     

  public function testCreateFlatFileObject() {
    //passing a empty file should produce a false result  
    $emptyFile = 'empty.csv'; 

    touch(\storage_path()."/app/flatfiles/". $emptyFile);
    
    $this->assertFalse($this->helper->createFltFleObj('flatfiles/'. $emptyFile));

    // test tables, files and folders that were created
    $this->testHelper->cleanupTestUploads([$emptyFile]);    
  }    

}
