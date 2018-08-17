<?php

use App\Libraries\CSVHelper;
use App\Libraries\FileViewHelper;
use App\Libraries\TableHelper;
use App\Libraries\TestHelper;

class FileViewHelperTest extends TestCase
{
    protected $fileViewHelper;
    private $singlefilewithpath;

    public function setUp() {
         parent::setUp();
         Artisan::call('migrate');
         Artisan::call('db:seed');
         $this->fileViewHelper = new FileViewHelper();
         $this->singlefilewithpath = '..\documents\BlobExport\indivletters\114561.txt';
    }

    protected function tearDown() {
         Artisan::call('migrate:reset');
         unset($this->singlefilewithpath);
         unset($this->fileViewHelper);
         parent::tearDown();
    }

    public function testGetFilename() {
        $output = $this->fileViewHelper->getFilename($this->singlefilewithpath);
        $this->assertEquals('114561.txt', $output, 'getFilename failed to get filename from String');
    }

    public function testGetFolderName() {
        $output = $this->fileViewHelper->getFolderName($this->singlefilewithpath);
        $this->assertEquals('indivletters', $output, 'getFolderName failed to get folder from String');
    }

    public function testfileExists() {
        // set filename in same format as we see in the rockefeller database
        $folder = $this->fileViewHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->fileViewHelper->getFilename($this->singlefilewithpath);

        // create fake table storage
        $path = './storage/app/testtable1';
        mkdir($path);
        mkdir($path.'/'.$folder);
        // create empty file
        touch($path.'/'.$folder.'/'.$filename, time() - (60 * 60 * 24 * 5));

        // check that file exists using our function
        $this->assertTrue($this->fileViewHelper->fileExists('testtable1', $folder.'/'.$filename));

        // cleanup delete folders and file that we created
        unlink($path.'/'.$folder.'/'.$filename);
        rmdir($path.'/'.$folder);
        rmdir($path);
    }

    public function testfileExistsInFolder() {
        $folder = $this->fileViewHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->fileViewHelper->getFilename($this->singlefilewithpath);

        // create fake table storage
        $path = './storage/app/testtable1';
        mkdir($path);
        mkdir($path.'/'.$folder);
        // create empty file
        touch($path.'/'.$folder.'/'.$filename, time() - (60 * 60 * 24 * 5));

        // check that file exists using our function
        $this->assertTrue($this->fileViewHelper->fileExistsInFolder('testtable1', $this->singlefilewithpath));

        // cleanup delete folders and file that we created
        unlink($path.'/'.$folder.'/'.$filename);
        rmdir($path.'/'.$folder);
        rmdir($path);
    }

    public function testfileDoesNotExists() {
        $folder = $this->fileViewHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->fileViewHelper->getFilename($this->singlefilewithpath);

        // check that file doesn't exists using our function
        $this->assertFalse($this->fileViewHelper->fileExists('testtable1', $folder.'/'.'notarealfile.txt'));
    }

    public function testfileDoesNotExistsInFolder() {
        $folder = $this->fileViewHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->fileViewHelper->getFilename($this->singlefilewithpath);

        // check that file doesn't exists using our function
        $this->assertFalse($this->fileViewHelper->fileExistsInFolder('testtable1', $this->singlefilewithpath));
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

      mkdir('./storage/app'.'/'.$tableName.'/'.'testing');

      copy('./storage/app'.'/'.$storageFolder.'/'.$testUpload, './storage/app'.'/'.$tableName.'/'.'testing'.'/'.$testUpload);

      $path = $this->fileViewHelper->locateFile(1, $testUpload);
      $this->assertEquals($path, $tableName.'/'.'testing'.'/'.$testUpload);

      unlink('./storage/app'.'/'.$tableName.'/'.'testing'.'/'.$testUpload);
      rmdir('./storage/app'.'/'.$tableName.'/'.'testing');

      // drop test table
      Schema::dropIfExists($tableName);

      // clear folder that was created with the table
      rmdir('./storage/app'.'/'.$tableName);
    }
}
