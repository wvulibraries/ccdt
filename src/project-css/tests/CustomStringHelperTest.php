<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Libraries\CustomStringHelper;

class CustomStringHelperTest extends TestCase
{
    protected $stringHelper;

    protected function setUp() {
        $this->stringHelper = new customStringHelper();
    }

    protected function tearDown() {
        unset($this->stringHelper);
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
        $input = '..\documents\BlobExport\indivletters\114561.txt';
        $output = $this->stringHelper->getFilename($input);
        $this->assertEquals('114561.txt', $output, 'getFilename failed to get filename from String');
    }

    public function testGetFolderName() {
        $input = '..\documents\BlobExport\indivletters\114561.txt';
        $output = $this->stringHelper->getFolderName($input);
        $this->assertEquals('indivletters', $output, 'getFolderName failed to get folder from String');
    }

    public function testSeparateFiles() {
        // check array that is returned from separateFiles to verify that the entries are correct
        $input = '..\documents\BlobExport\indivletters\114561.txt^..\documents\BlobExport\indivletters\114562.txt';
        $output = $this->stringHelper->separateFiles($input);
        $this->assertEquals('..\documents\BlobExport\indivletters\114561.txt', $output[0]);
        $this->assertEquals('..\documents\BlobExport\indivletters\114562.txt', $output[1]);
    }


}
