<?php

use App\Libraries\CustomStringHelper;

class CustomStringHelperTest extends TestCase
{
    protected $stringHelper;
    private $singlefilewithpath;

    public function setUp(): void {
         parent::setUp();
         Artisan::call('migrate:refresh --seed');
         $this->stringHelper = new customStringHelper();
         $this->singlefilewithpath = '..\documents\BlobExport\indivletters\114561.txt';
    }

    protected function tearDown(): void {
         #Artisan::call('migrate:reset');
         unset($this->singlefilewithpath);
         unset($this->stringHelper);
         parent::tearDown();
    }

    public function testRemoveCommonWords() {
        $output = $this->stringHelper->removeCommonWords(explode(" ", "this is a test string"));
        $this->assertEquals('test string', implode(" ", $output), 'removeCommonWords failed to remove common words');
    }

    public function testSeparateFiles() {
        // check array that is returned from separateFiles to verify that the entries are correct
        $input = '..\documents\BlobExport\indivletters\114561.txt^..\documents\BlobExport\indivletters\114562.txt';
        $output = $this->stringHelper->separateFiles($input);
        $this->assertEquals('..\documents\BlobExport\indivletters\114561.txt', $output[ 0 ]);
        $this->assertEquals('..\documents\BlobExport\indivletters\114562.txt', $output[ 1 ]);
    }

    public function testCheckForSSN() {
        $this->assertTrue($this->stringHelper->ssnExists(file_get_contents('./storage/app/files/test/fake_socials.txt')));
    }

    public function testRedactSSN() {
        // test should remove ssn from file and then ssnExists should report false that it exists
        $contents = $this->stringHelper->ssnRedact(file_get_contents('./storage/app/files/test/fake_socials.txt'));
        $this->assertFalse($this->stringHelper->ssnExists($contents));
    }

    public function testCheckForFilenames() {
        // test should detect filenames included in the string that has mixed other data with the filenames
        // in the original data the path doesn't match what is in the local file dump and the files are located
        // in formletters with the addition of .txt to the filenames.
        $contents = 'this is a sample entry to mimic what is seen in the real database this was created on 12-18-17TAM Example logged in 12/18/2017TAM Document created: #171218PMH_A0001 12/18/2017TAM Letter logged out 12/18/2017/public/form/crime/crime_01.doc/public/form/civil_rights/cr_01.doc/public/form/child_youth/cy_01.doc//public/form/education/ed_03.doc/public/form/civil_rights/cr_02.doc/form/social_security/sos_01.doc/public/form/foreign_affairs/foir_01.doc/public/form/miscellaneous/misc_01.doc';
        $listArray = $this->stringHelper->checkForFilenames($contents);
        //the first position of the array should contain the string 'cr_01.doc'
        $this->assertTrue(strcmp('crime_01.doc', $listArray[0]) == 0);
    }

}
