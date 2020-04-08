<?php

use App\Models\Collection;
use App\Models\Table;
use App\Libraries\CSVHelper;
use App\Libraries\FileViewHelper;
use App\Helpers\TableHelper;

class FileViewHelperTest extends BrowserKitTestCase
{
    protected $fileViewHelper;
    private $singlefilewithpath;

    public function setUp(): void {
         parent::setUp();
         //Artisan::call('migrate:refresh --seed');
         $this->fileViewHelper = new FileViewHelper();
         $this->singlefilewithpath = '..\documents\BlobExport\indivletters\114561.txt';
    }

    protected function tearDown(): void {
         //Artisan::call('migrate:reset');
         unset($this->singlefilewithpath);
         unset($this->fileViewHelper);
         parent::tearDown();
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

    public function testGetFolderName() {
        $output = $this->fileViewHelper->getFolderName($this->singlefilewithpath);
        $this->assertEquals('indivletters', $output, 'getFolderName failed to get folder from String');
    }

    public function testGetFolderNamewithInvalidString() {
      $output = $this->fileViewHelper->getFolderName('not a string with a path to a file');
      $this->assertFalse($output);
    }

    public function testGetFilename() {
        $output = $this->fileViewHelper->getFilename($this->singlefilewithpath);
        $this->assertEquals('114561.txt', $output, 'getFilename failed to get filename from String');
    }

    public function testBuildFileLink() {
        $colNme = time(); // random collection name
        $output = $this->fileViewHelper->buildFileLink($colNme, $this->singlefilewithpath);
        $this->assertEquals($colNme.'/indivletters/114561.txt', $output, 'buildFileLink failed to get generate the correct path');
    }

    public function testBuildFileLinkWithoutFolder() {
        $colNme = time(); // random collection name
        $output = $this->fileViewHelper->buildFileLink($colNme, '114561.txt');
        $this->assertEquals($colNme.'/114561.txt', $output, 'buildFileLink failed to get generate the correct path');
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
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = 'zillow.csv';

        // set location of file
        $testUpload = 'test_upload.doc';

        $tableName = (new TestHelper)->createTable($storageFolder, $fileName, true);

        //get table
        $table = Table::where('tblNme', $tableName)->first();

        // find the collection
        $collection = Collection::findorFail($table->collection_id); 

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
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = 'zillow.csv';

        // set fake filename to look for
        $testUpload = 'test.doc';

        $tableName = (new TestHelper)->createTable($storageFolder, $fileName, true);

        //get table
        $table = Table::where('tblNme', $tableName)->first();

        // find the collection
        $collection = Collection::findorFail($table->collection_id); 

        mkdir('./storage/app'.'/'.$collection->clctnName.'/'.'testing');

        $result = $this->fileViewHelper->locateFile(1, $testUpload);
        $this->assertFalse($result);

        // drop test table
        Schema::dropIfExists($tableName);

        // clear folder that was created with the collection
        File::deleteDirectory('./storage/app'.'/'.$collection->clctnName);  
  }

  public function testGetFilePath() {
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = 'test.dat';

        // set fake filename to look for
        $testUpload = '000006.txt';

        $tableName = (new TestHelper)->createTable($storageFolder, $fileName, true);

        //get table
        $table = Table::where('tblNme', $tableName)->first();

        // find the collection
        $collection = Collection::findorFail($table->collection_id);          

        $folder = 'indivletters';

        mkdir('./storage/app'.'/'.$collection->clctnName.'/'.$folder);

        // create empty file
        touch('./storage/app'.'/'.$collection->clctnName.'/'.$folder.'/'.$testUpload, time() - (60 * 60 * 24 * 5));

        $result = $this->fileViewHelper->getFilePath(1, 1, $testUpload);

        $this->assertEquals($result, $collection->clctnName.'/'.$folder.'/000006.txt');

        // drop test table
        Schema::dropIfExists($tableName);

        // clear folder that was created with the collection
        File::deleteDirectory('./storage/app'.'/'.$collection->clctnName);          
  }

}
