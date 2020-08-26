<?php

use App\Adapters\ImportAdapter;
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
    private $colName;
    private $tableName;    
    public $importAdapter; 

    public function setUp(): void {
        parent::setUp();
        $this->fileViewHelper = new FileViewHelper;
        $this->singlefilewithpath = '..\documents\BlobExport\indivletters\114561.txt';
        $this->tableHelper = new TableHelper;  
        $this->collectionHelper = new CollectionHelper; 
        $this->importAdapter = new ImportAdapter;
        
        // Generate Collection Name
        $this->colName = $this->testHelper->generateCollectionName();

        // Generate Table Name
        $this->tableName = $this->testHelper->createTableName();         
    }

    protected function tearDown(): void {
        // test tables, files and folders that were created
        $this->testHelper->cleanupTestTables();

        // Delete Test Collections
        $this->testHelper->deleteTestCollections();         

        parent::tearDown();
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
        $path = './storage/app/'.$this->colName;
        mkdir($path);
        mkdir($path.'/'.$folder);
        // create empty file
        touch($path.'/'.$folder.'/'.$filename, time() - (60 * 60 * 24 * 5));

        // check that file exists using our function
        $this->assertTrue($this->fileViewHelper->fileExists($this->colName, $folder.'/'.$filename));

        // cleanup delete folders that we created
        File::deleteDirectory($path);  
    }

    public function testfileExistsInFolder() {
        $folder = $this->fileViewHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->fileViewHelper->getFilename($this->singlefilewithpath);

        // create fake collection storage
        $path = './storage/app/'.$this->colName;
        mkdir($path);
        mkdir($path.'/'.$folder);
        // create empty file
        touch($path.'/'.$folder.'/'.$filename, time() - (60 * 60 * 24 * 5));

        // check that file exists using our function
        $this->assertTrue($this->fileViewHelper->fileExistsInFolder($this->colName, $this->singlefilewithpath));

        // cleanup delete folders that we created
        File::deleteDirectory($path);     
    }

    public function testfileDoesNotExistsInFolder() {
        // create fake collection storage
        $folder = 'testfolder';
        $path = './storage/app/'.$this->colName;

        mkdir($path);
        mkdir($path.'/'.$folder);

        // check that file exists using our function
        $this->assertFalse($this->fileViewHelper->fileExistsInFolder($this->colName, $folder.'/'.'notarealfile.txt'));

        // cleanup delete folders that we created
        File::deleteDirectory($path);        
    }

    public function testGetFolderNamewithInvalidString() {
      $output = $this->fileViewHelper->getFolderName('not a string with a path to a file');
      $this->assertFalse($output);
    }

    public function testBuildFileLink() {
        $output = $this->fileViewHelper->buildFileLink($this->colName, $this->singlefilewithpath);
        $this->assertEquals($this->colName.'/indivletters/114561.txt', $output, 'buildFileLink failed to get generate the correct path');
    }

    public function testBuildFileLinkWithoutFolder() {
        $fileName = '114561.txt';
        $output = $this->fileViewHelper->buildFileLink($this->colName, $fileName);
        $this->assertEquals($this->colName.'/'.$fileName, $output, 'buildFileLink failed to get generate the correct path');
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
        $collection = $this->testHelper->createCollection($this->colName, 1, false);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection, 'zillow.csv');

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
    }

    public function testLocateFilewithinvalidFile() {
        // Create Test Collection        
        $collection = $this->testHelper->createCollection($this->colName, 1, false);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection, 'zillow.csv');

        //get table
        $table = Table::where('tblNme', $tableName)->first();

        // set fake filename to look for
        $testUpload = 'test.doc';

        mkdir('./storage/app'.'/'.$collection->clctnName.'/'.'testing');

        $result = $this->fileViewHelper->locateFile(1, $testUpload);
        $this->assertFalse($result);
  }

  public function testGetFilePathNoRecord() {
        // Call helper create
        $collection = $this->testHelper->createCollection($this->colName, 1, false);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection, 'test.dat');

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
  }

  public function testGetFilePath() {
        // Generate Test Collection
        $collection = $this->testHelper->createCollection($this->colName, 0);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection, 'test.dat');

        $this->importAdapter->process($tableName, 'files/test', 'test.dat');

        //get table
        $table = Table::where('tblNme', $tableName)->first();
        
        // set fake filename to look for
        $testUpload = '000007.txt';        

        $folder = 'indivletters';

        mkdir('./storage/app'.'/'.$collection->clctnName.'/'.$folder);

        // create empty file
        touch('./storage/app'.'/'.$collection->clctnName.'/'.$folder.'/'.$testUpload, time() - (60 * 60 * 24 * 5));

        $result = $this->fileViewHelper->getFilePath(1, 2, $testUpload);

        $this->assertEquals($result, $collection->clctnName.'/'.$folder.'/'.$testUpload);      
  }  

  public function testgetOriginalPath() {
        // Generate Test Collection
        $collection = $this->testHelper->createCollection($this->colName, 0);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection, 'test.dat');

        $this->importAdapter->process($tableName, 'files/test', 'test.dat');

        //get table
        $table = Table::where('tblNme', $tableName)->first();
        
        // set fake filename to look for
        $testUpload = '000007.txt';        

        $folder = 'indivletters';

        mkdir('./storage/app'.'/'.$collection->clctnName.'/'.$folder);

        // create empty file
        touch('./storage/app'.'/'.$collection->clctnName.'/'.$folder.'/'.$testUpload, time() - (60 * 60 * 24 * 5));

        $result = $this->fileViewHelper->getOriginalPath(1, 2, $testUpload);

        $this->assertEquals($result, '..\documents\BlobExport\indivletters\000007.txt');      
  }

}
