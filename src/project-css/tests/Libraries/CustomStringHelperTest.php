<?php

class CustomStringHelperTest extends TestCase
{
    protected $stringHelper;
    private $singlefilewithpath;

    protected function setUp() {
        parent::setUp();
        $this->stringHelper = new customStringHelper();
        $this->singlefilewithpath = '..\documents\BlobExport\indivletters\114561.txt';
    }

    protected function tearDown() {
        unset($this->singlefilewithpath);
        unset($this->stringHelper);
        parent::tearDown();
    }

    /**
     * Clean Search string replaces ? with * for fulltext searches
     * Also it will remove extra spaces in the string
     * And converts all characters to lower case
     *
     * @return void
     */
    public function testCleanString() {
        // test should replace ? with *
        $this->assertEquals('test*', $this->stringHelper->cleanSearchString('test?'), 'cleanSearchString failed to replace ? with *');

        // test should convert string to lower case
        $this->assertEquals('test', $this->stringHelper->cleanSearchString('TEST'), 'cleanSearchString failed to convert string to lower case');

        // test should remove extra spaces around keyword
        $this->assertEquals('test', $this->stringHelper->cleanSearchString(' test '), 'cleanSearchString failed to remove extra spaces');
    }

    public function testGetFilename() {
        $output = $this->stringHelper->getFilename($this->singlefilewithpath);
        $this->assertEquals('114561.txt', $output, 'getFilename failed to get filename from String');
    }

    public function testGetFolderName() {
        $output = $this->stringHelper->getFolderName($this->singlefilewithpath);
        $this->assertEquals('indivletters', $output, 'getFolderName failed to get folder from String');
    }

    public function testSeparateFiles() {
        // check array that is returned from separateFiles to verify that the entries are correct
        $input = '..\documents\BlobExport\indivletters\114561.txt^..\documents\BlobExport\indivletters\114562.txt';
        $output = $this->stringHelper->separateFiles($input);
        $this->assertEquals('..\documents\BlobExport\indivletters\114561.txt', $output[0]);
        $this->assertEquals('..\documents\BlobExport\indivletters\114562.txt', $output[1]);
    }

    public function testfileExists() {
        // set filename in same format as we see in the rockefeller database
        $folder = $this->stringHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->stringHelper->getFilename($this->singlefilewithpath);

        // create fake table storage
        $path = './storage/app/testtable1';
        mkdir($path);
        mkdir($path . '/' . $folder);
        // create empty file
        touch($path . '/' . $folder . '/' . $filename, time() - (60 * 60 * 24 * 5));

        // check that file exists using our function
        $this->assertTrue($this->stringHelper->fileExists('testtable1', $folder . '/' . $filename));

        // cleanup delete folders and file that we created
        unlink($path . '/' . $folder . '/' . $filename);
        rmdir($path . '/' . $folder);
        rmdir($path);
    }

    public function testfileExistsInFolder() {
        $folder = $this->stringHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->stringHelper->getFilename($this->singlefilewithpath);

        // create fake table storage
        $path = './storage/app/testtable1';
        mkdir($path);
        mkdir($path . '/' . $folder);
        // create empty file
        touch($path . '/' . $folder . '/' . $filename, time() - (60 * 60 * 24 * 5));

        // check that file exists using our function
        $this->assertTrue($this->stringHelper->fileExistsInFolder('testtable1', $this->singlefilewithpath));

        // cleanup delete folders and file that we created
        unlink($path . '/' . $folder . '/' . $filename);
        rmdir($path . '/' . $folder);
        rmdir($path);
    }

    public function testfileDoesNotExists() {
        $folder = $this->stringHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->stringHelper->getFilename($this->singlefilewithpath);

        // check that file doesn't exists using our function
        $this->assertFalse($this->stringHelper->fileExists('testtable1', $folder . '/' . $filename));
    }

    public function testfileDoesNotExistsInFolder() {
        $folder = $this->stringHelper->getFolderName($this->singlefilewithpath);
        $filename = $this->stringHelper->getFilename($this->singlefilewithpath);

        // check that file doesn't exists using our function
        $this->assertFalse($this->stringHelper->fileExistsInFolder('testtable1', $this->singlefilewithpath));
    }


    public function testCheckForSSN(){
        $this->assertTrue($this->stringHelper->ssnExists(file_get_contents('./storage/app/files/test/fake_socials.txt')));
    }

    public function testRedactSSN(){
        // test should remove ssn from file and then ssnExists should report false that it exists
        $contents = $this->stringHelper->ssnRedact(file_get_contents('./storage/app/files/test/fake_socials.txt'));
        $this->assertFalse($this->stringHelper->ssnExists($contents));
    }

}
