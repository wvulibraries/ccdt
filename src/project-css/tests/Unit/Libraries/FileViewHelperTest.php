<?php

use App\Libraries\CSVHelper;
use App\Libraries\FileViewHelper;
use App\Libraries\TableHelper;
use App\Libraries\TestHelper;

class FileViewHelperTest extends TestCase
{
    protected $fileViewHelper;
    private $singlefilewithpath;

    public function setUp(): void {
         parent::setUp();
         Artisan::call('migrate');
         Artisan::call('db:seed');
         $this->fileViewHelper = new FileViewHelper();
         $this->singlefilewithpath = '..\documents\BlobExport\indivletters\114561.txt';
    }

    protected function tearDown(): void {
         Artisan::call('migrate:reset');
         unset($this->singlefilewithpath);
         unset($this->fileViewHelper);
         parent::tearDown();
    }

    public function testfileExists() {
        // set filename in same format as we see in the rockefeller database
        $folder = $this->fileViewHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->fileViewHelper->getFilename($this->singlefilewithpath);

        // create fake table storage
        $tblNme = time();
        $path = './storage/app/'.$tblNme;
        mkdir($path);
        mkdir($path.'/'.$folder);
        // create empty file
        touch($path.'/'.$folder.'/'.$filename, time() - (60 * 60 * 24 * 5));

        // check that file exists using our function
        $this->assertTrue($this->fileViewHelper->fileExists($tblNme, $folder.'/'.$filename));

        // cleanup delete folders and file that we created
        unlink($path.'/'.$folder.'/'.$filename);
        rmdir($path.'/'.$folder);
        rmdir($path);
    }

    public function testfileExistsInFolder() {
        $folder = $this->fileViewHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->fileViewHelper->getFilename($this->singlefilewithpath);

        // create fake table storage
        $tblNme = time();
        $path = './storage/app/'.$tblNme;
        mkdir($path);
        mkdir($path.'/'.$folder);
        // create empty file
        touch($path.'/'.$folder.'/'.$filename, time() - (60 * 60 * 24 * 5));

        // check that file exists using our function
        $this->assertTrue($this->fileViewHelper->fileExistsInFolder($tblNme, $this->singlefilewithpath));

        // cleanup delete folders and file that we created
        unlink($path.'/'.$folder.'/'.$filename);
        rmdir($path.'/'.$folder);
        rmdir($path);
    }

    public function testfileDoesNotExistsInFolder() {
        // create fake table storage
        $tblNme = time();
        $folder = 'testfolder';
        $path = './storage/app/'.$tblNme;

        mkdir($path);
        mkdir($path.'/'.$folder);

        // check that file exists using our function
        $this->assertFalse($this->fileViewHelper->fileExistsInFolder($tblNme, $folder.'/'.'notarealfile.txt'));

        // cleanup delete folders that we created
        rmdir($path.'/'.$folder);
        rmdir($path);
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
        $tblNme = time(); // random table name
        $output = $this->fileViewHelper->buildFileLink($tblNme, $this->singlefilewithpath);
        $this->assertEquals($tblNme.'/indivletters/114561.txt', $output, 'buildFileLink failed to get generate the correct path');
    }

    public function testBuildFileLinkWithoutFolder() {
        $tblNme = time(); // random table name
        $output = $this->fileViewHelper->buildFileLink($tblNme, '114561.txt');
        $this->assertEquals($tblNme.'/114561.txt', $output, 'buildFileLink failed to get generate the correct path');
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

    public function testLocateFilewithinvalidFile() {
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = 'zillow.csv';

        // set fake filename to look for
        $testUpload = 'test.doc';

        $tableName = (new TestHelper)->createTable($storageFolder, $fileName, true);

        mkdir('./storage/app'.'/'.$tableName.'/'.'testing');

        $result = $this->fileViewHelper->locateFile(1, $testUpload);
        $this->assertFalse($result);

        rmdir('./storage/app'.'/'.$tableName.'/'.'testing');

        // drop test table
        Schema::dropIfExists($tableName);

        // clear folder that was created with the table
        rmdir('./storage/app'.'/'.$tableName);
  }

  public function testGetFilePath() {
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = 'test.dat';

        // set fake filename to look for
        $testUpload = '000006.txt';

        $tableName = (new TestHelper)->createTable($storageFolder, $fileName, true);

        $folder = 'indivletters';

        mkdir('./storage/app'.'/'.$tableName.'/'.$folder);

        // create empty file
        touch('./storage/app'.'/'.$tableName.'/'.$folder.'/'.$testUpload, time() - (60 * 60 * 24 * 5));

        $result = $this->fileViewHelper->getFilePath(1, 1, $testUpload);

        $this->assertEquals($result, $tableName.'/'.$folder.'/000006.txt');

        unlink('./storage/app'.'/'.$tableName.'/'.$folder.'/'.$testUpload);

        // clear folder that was created with the table
        rmdir('./storage/app'.'/'.$tableName.'/'.$folder);         
        rmdir('./storage/app'.'/'.$tableName);

        // drop test table
        Schema::dropIfExists($tableName);
  }

}
