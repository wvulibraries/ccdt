<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Libraries\tikaConvert;

class tikaConvertTest extends TestCase
{
    protected $tikaConvert;
    private $singlefilewithpath;

    protected function setUp() {
        parent::setUp();
        $this->tikaConvert = new tikaConvert();
        $this->singlefilewithpath = './storage/app/files/test/test_upload.doc';
    }

    protected function tearDown() {
        unset($this->singlefilewithpath);
        unset($this->tikaConvert);
        parent::tearDown();
    }

    public function testConvert(){
        $contents = $this->tikaConvert->convert($this->singlefilewithpath);
        $this->assertTrue(strpos($contents, 'testing') !== false);
    }

}
