<?php

use App\Helpers\CollectionHelper;
use App\Helpers\CSVHelper;
use App\Helpers\FileViewHelper;
use App\Helpers\TableHelper;
use App\Models\Collection;
use App\Models\Table;

class FileViewHelperTest extends BrowserKitTestCase
{
    protected $fileViewHelper;
    private $singlefilewithpath;
    public $collectionHelper;
    public $tableHelper;    

    public function setUp(): void {
        parent::setUp();
        $this->fileViewHelper = new FileViewHelper;
        $this->singlefilewithpath = '..\documents\BlobExport\indivletters\114561.txt';
        $this->tableHelper = new TableHelper;  
        $this->collectionHelper = new CollectionHelper;         
    }

    public function testGetFolderName() {
      // Test with Correctly formatted path
      $output = $this->fileViewHelper->getFolderName($this->singlefilewithpath);       
      $this->assertEquals('indivletters', $output, 'getFolderName failed to get folder from String');
      // Test with a invalid string
      $output = $this->fileViewHelper->getFolderName('This is not a file path string');
      // Incorrect path should give us false
      $this->assertFalse($output);
    }
    
    public function testGetFilename() {
        $output = $this->fileViewHelper->getFilename($this->singlefilewithpath);
        $this->assertEquals('114561.txt', $output, 'getFilename failed to get filename from String');   
    }    

    public function testfileExists() {
        // set filename in same format as we see in the rockefeller database
        $folder = $this->fileViewHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->fileViewHelper->getFilename($this->singlefilewithpath);

        // create fake collection storage
        $colNme = time();
        $path = './storage/app/'.$colNme;
        mkdir($path);
        mkdir($path.'/'.$folder);
        // create empty file
        touch($path.'/'.$folder.'/'.$filename, time() - (60 * 60 * 24 * 5));

        // check that file exists using our function
        $this->assertTrue($this->fileViewHelper->fileExists($colNme, $folder.'/'.$filename));

        // cleanup delete folders that we created
        File::deleteDirectory($path);  
    }

    public function testfileExistsInFolder() {
        $folder = $this->fileViewHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->fileViewHelper->getFilename($this->singlefilewithpath);

        // create fake collection storage
        $colNme = time();
        $path = './storage/app/'.$colNme;
        mkdir($path);
        mkdir($path.'/'.$folder);
        // create empty file
        touch($path.'/'.$folder.'/'.$filename, time() - (60 * 60 * 24 * 5));

        // check that file exists using our function
        $this->assertTrue($this->fileViewHelper->fileExistsInFolder($colNme, $this->singlefilewithpath));

        // cleanup delete folders that we created
        File::deleteDirectory($path);     
    }

    public function testfileDoesNotExistsInFolder() {
        // create fake collection storage
        $colNme = time();
        $folder = 'testfolder';
        $path = './storage/app/'.$colNme;

        mkdir($path);
        mkdir($path.'/'.$folder);

        // check that file exists using our function
        $this->assertFalse($this->fileViewHelper->fileExistsInFolder($colNme, $folder.'/'.'notarealfile.txt'));

        // cleanup delete folders that we created
        File::deleteDirectory($path);        
    }

    public function testGetFolderNamewithInvalidString() {
      $output = $this->fileViewHelper->getFolderName('not a string with a path to a file');
      $this->assertFalse($output);
    }

    public function testBuildFileLink() {
        $colNme = time(); // random collection name
        $output = $this->fileViewHelper->buildFileLink($colNme, $this->singlefilewithpath);
        $this->assertEquals($colNme.'/indivletters/114561.txt', $output, 'buildFileLink failed to get generate the correct path');
    }

    public function testBuildFileLinkWithoutFolder() {
        $colNme = time(); // random collection name
        $fileName = '114561.txt';
        $output = $this->fileViewHelper->buildFileLink($colNme, $fileName);
        $this->assertEquals($colNme.'/'.$fileName, $output, 'buildFileLink failed to get generate the correct path');
    }

    public function testGetFileContents() {
        $storageFolder = 'files/test';
        // set location of file
        $fileName = 'test_upload.doc';
        $path = $storageFolder.'/'.$fileName;
        $source = storage_path('app/'.$path);
        $fileContents = $this->fileViewHelper->getFileContents($source);

        // verify we have correct object type
        $this->assertTrue(is_a($fileContents, 'Illuminate\Http\Response'));
        // assert that content has the word testing
        $this->assertRegexp('/testing/', $fileContents->content());
    }

    public function testLocateFile() {
        // Create Test Collection        
        $collection = $this->createTestCollection('TestCollection', false);

        // Create Test Table
        $tableName = $this->createTestTable($collection, 'zillow.csv');

        //get table
        $table = Table::where('tblNme', $tableName)->first();

        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $testUpload = 'test_upload.doc';

        mkdir('./storage/app'.'/'.$collection->clctnName.'/'.'testing');

        copy('./storage/app'.'/'.$storageFolder.'/'.$testUpload, './storage/app'.'/'.$collection->clctnName.'/'.'testing'.'/'.$testUpload);

        $path = $this->fileViewHelper->locateFile(1, $testUpload);
        $this->assertEquals($path, $collection->clctnName.'/'.'testing'.'/'.$testUpload);

        // drop test table
        Schema::dropIfExists($tableName);

        // clear folder that was created with the collection
        File::deleteDirectory('./storage/app'.'/'.$collection->clctnName);    
    }

    public function testLocateFilewithinvalidFile() {
        // Create Test Collection        
        $collection = $this->createTestCollection('TestCollection', false);

        // Create Test Table
        $tableName = $this->createTestTable($collection, 'zillow.csv');

        //get table
        $table = Table::where('tblNme', $tableName)->first();

        // set fake filename to look for
        $testUpload = 'test.doc';

        mkdir('./storage/app'.'/'.$collection->clctnName.'/'.'testing');

        $result = $this->fileViewHelper->locateFile(1, $testUpload);
        $this->assertFalse($result);

        // drop test table
        Schema::dropIfExists($tableName);

        // clear folder that was created with the collection
        File::deleteDirectory('./storage/app'.'/'.$collection->clctnName);  
  }

  public function testGetFilePathNoRecord() {
        // Call helper create
        $collection = $this->createTestCollection('TestCollection', false);

        // Create Test Table
        $tableName = $this->createTestTable($collection, 'test.dat');

        //get table
        $table = Table::where('tblNme', $tableName)->first();
        
        // set fake filename to look for
        $testUpload = '000006.txt';        

        $folder = 'indivletters';

        mkdir('./storage/app'.'/'.$collection->clctnName.'/'.$folder);

        // create empty file
        touch('./storage/app'.'/'.$collection->clctnName.'/'.$folder.'/'.$testUpload, time() - (60 * 60 * 24 * 5));

        $result = $this->fileViewHelper->getFilePath($table->id, 1, $testUpload);

        $this->assertEquals($result, $collection->clctnName.'/'.$folder.'/'.$testUpload);

        // drop test table
        Schema::dropIfExists($tableName);

        // clear folder that was created with the collection
        File::deleteDirectory('./storage/app'.'/'.$collection->clctnName);          
  }

//   public function testgetOriginalPath() {
//         // Call helper create
//         $collection = $this->createTestCollection('TestCollection', false);

//         // Create Test Table
//         $tableName = $this->createTestTable($collection, 'test.dat');

//         //get table
//         $table = Table::where('tblNme', $tableName)->first();

//         var_dump($table->all());
        
//         // set fake filename to look for
//         $testUpload = 'feds0101_dc_statehood_1990.doc';        

//         $folder = 'federal_government';

//         mkdir('./storage/app'.'/'.$collection->clctnName.'/'.$folder);

//         // create empty file
//         touch('./storage/app'.'/'.$collection->clctnName.'/'.$folder.'/'.$testUpload, time() - (60 * 60 * 24 * 5));

//         $result = $this->fileViewHelper->getOriginalPath($table->id, 1, $testUpload);

//         $this->assertEquals($result, $collection->clctnName.'/'.$folder.'/'.$testUpload);

//         // drop test table
//         Schema::dropIfExists($tableName);

//         // delete folder and contents that was created for the test
//         File::deleteDirectory('./storage/app'.'/'.$collection->clctnName);          
//   }

    private function createTestTable($collection, $fileName) {
        // set storage location
        $storageFolder = 'files/test';
        
        // Test Table Name
        $tableName = 'test'.time();

        // Create Table and Dispatch file Import
        $this->tableHelper->importFile($storageFolder, $fileName, $tableName, $collection->id, $collection->isCms);
        
        // return table name
        return ($tableName);
    }
    
    private function createTestCollection($name, $isCms) {
        // Create Collection Test Data Array
        $data = [
          'isCms' => $isCms,
          'name' => $name
        ];

        // Call collection helper create
        return($this->collectionHelper->create($data));         
    }  


}
