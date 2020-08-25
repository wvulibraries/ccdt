<?php
  # app/tests/Unit/Models/CollectionUnitTest.php

  use App\Helpers\CollectionHelper;
  use App\Models\Collection;
  use Vendor\fzaninotto\Faker\Generator;

  class CollectionUnitTest extends TestCase
  {
    private $colName;

    public function setUp(): void {
      parent::setUp();

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();         
    }

    protected function tearDown(): void {
      // Delete Test Collections
      $this->testHelper->deleteTestCollections();         

      parent::tearDown();
    } 

    public function testCollectionHasFiles() {
      // Create Test Data Array
      $data = [
            'isCms' => false,
            'name' => $this->colName
      ];

      // Call helper create
      $collection = (new CollectionHelper)->create($data);

      // assert no files exist
      $this->assertFalse($collection->hasFiles());

      // Create Empty file in storage folder for collection
      $path = './storage/app';
      $file = 'empty.csv';      

      $emptyFile = $path.'/'.$this->colName.'/'.$file;
      touch($emptyFile);      

      $this->assertTrue($collection->hasFiles());
    }

    public function testCollectionHasTables() {
      // Create Test Data Array
      $data = [
            'isCms' => false,
            'name' => $this->colName
      ];

      // Call helper create
      $collection = (new CollectionHelper)->create($data);

      // assert no files exist
      $this->assertFalse($collection->hasTables());

      // Create Test Table
      $tableName = $this->testHelper->createTestTable($collection, 'test.dat');      

      $this->assertTrue($collection->hasTables());
    }    

  }
?>
