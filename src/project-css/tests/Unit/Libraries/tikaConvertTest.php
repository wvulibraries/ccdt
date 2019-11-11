<?php

use App\Libraries\tikaConvert;

class tikaConvertTest extends TestCase
{
    protected $tikaConvert;
    private $singlefilewithpath;

    protected function setUp(): void {
        parent::setUp();
        $this->tikaConvert = new tikaConvert();
        $this->singlefilewithpath = './storage/app/files/test/test_upload.doc';
    }

    protected function tearDown(): void {
        unset($this->singlefilewithpath);
        unset($this->tikaConvert);
        parent::tearDown();
    }

    // public function testConvertValidFile() {
    //     $contents = $this->tikaConvert->convert($this->singlefilewithpath);
    //     $this->assertTrue(strpos($contents, 'testing') !== false);
    // }

    // public function testConvertInvalidFile() {
    //     $contents = $this->tikaConvert->convert('invalid.doc');
    //     $this->assertFalse(strpos($contents, 'testing'));
    // }

    public function testInvalidTikaSettings() {
        $this->tikaConvert->setTikaHost('localhost');
        $this->tikaConvert->setTikaPort('9997');
        $this->assertFalse($this->tikaConvert->serverOpen());
    }

    public function testConvertWithInvalidTikaSettings() {
        $this->tikaConvert->setTikaHost('localhost');
        $this->tikaConvert->setTikaPort('9997');
        $contents = $this->tikaConvert->convert($this->singlefilewithpath);
        $this->assertFalse(strpos($contents, 'testing'));
    }
}
