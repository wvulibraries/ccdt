<?php

use App\Libraries\FullTextSearchFormatter;

class FullTextSearchFormatterTest extends TestCase
{
    protected $fullTextHelper;

    protected function setUp() {
        parent::setUp();
        $this->fullTextHelper = new fullTextSearchFormatter();
    }

    protected function tearDown() {
        parent::tearDown();
    }

    /**
     * Clean Search string replaces ? with * for fulltext searches
     * Also it will remove extra spaces in the string
     * And converts all characters to lower case
     *
     * @return void
     */
    public function testPrepareSearch() {
       // test should replace ? with *
       $this->assertEquals('test*', $this->fullTextHelper->prepareSearch('test?'), 'prepareSearch failed to replace ? with *');

       // test should convert string to lower case
       $this->assertEquals('test', $this->fullTextHelper->prepareSearch('TEST'), 'prepareSearch failed to convert string to lower case');

       // test should remove extra spaces around keyword
       $this->assertEquals('test', $this->fullTextHelper->prepareSearch(' test '), 'prepareSearch failed to remove extra spaces');

       // test should remove extra spaces around keyword
       $this->assertEquals('test', $this->fullTextHelper->prepareSearch(' test '), 'prepareSearch failed to remove extra spaces');

       // test should match this formatted query
       $this->assertEquals('nice language', $this->fullTextHelper->prepareSearch('nice language'), 'prepareSearch failed to remove extra spaces');

       // test should match this formatted query
       $this->assertEquals('+nice +language', $this->fullTextHelper->prepareSearch('+nice +language'), 'prepareSearch failed to remove extra spaces');

       // test should match this formatted query
       $this->assertEquals('+nice -language', $this->fullTextHelper->prepareSearch('+nice -language'), 'prepareSearch failed to remove extra spaces');

       // test should match this formatted query
       $this->assertEquals('+nice ~language', $this->fullTextHelper->prepareSearch('+nice ~language'), 'prepareSearch failed to remove extra spaces');

       // test should match this formatted query
       $this->assertEquals('+nice*', $this->fullTextHelper->prepareSearch('+nice*'), 'prepareSearch failed to remove extra spaces');

       // test should match this formatted query
       $this->assertEquals('"nice language"', $this->fullTextHelper->prepareSearch('"nice language"'), 'prepareSearch failed to remove extra spaces');

       // test should match this formatted query it should remove invalid characters @@ and --
       $this->assertEquals('"nice language"', $this->fullTextHelper->prepareSearch('"nice@@ language--"'), 'prepareSearch failed to remove invalid characters');

       // test should match this formatted query
       $this->assertEquals('+nice +(language country)', $this->fullTextHelper->prepareSearch('+nice +(language country)'), 'prepareSearch failed to remove extra spaces');

       // test should match this formatted query
       $this->assertEquals('+nice +language country', $this->fullTextHelper->prepareSearch('+nice +(language country'), 'prepareSearch failed to remove ( since the closing ) isnt present');

       // test should match this formatted query
       $this->assertEquals('+nice +(>language <country)', $this->fullTextHelper->prepareSearch('+nice +(>language <country)'), 'prepareSearch failed to remove extra spaces');
    }

    public function testCleanMatchEither() {
        // test should return false has no grouping for matching either
        $this->assertFalse($this->fullTextHelper->cleanMatchEither('test?'));
        // test should return true has Match Either grouping
        $this->assertEquals('+(<test >test2)', $this->fullTextHelper->cleanMatchEither('(<test >test2)'), 'cleanMatchEither failed to return properly formatted string');
    }

    public function testHasWildCard() {
        // test should return true since we have a wildchard
        $this->assertTrue($this->fullTextHelper->hasWildCard('test*'));
        // test should return false has no wildcard
        $this->assertFalse($this->fullTextHelper->hasWildCard('test?'));
        // test should return false has wildcard in incorrect position
        $this->assertFalse($this->fullTextHelper->hasWildCard('te*st'));
    }
}
