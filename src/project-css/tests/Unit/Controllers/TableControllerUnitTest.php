<?php
  # app/tests/Unit/controllers/TableControllerUnitTest.php

  use Illuminate\Support\Facades\Storage;
  use App\Http\Controllers\TableController;

  class TableControllerUnitTest extends BrowserKitTestCase
  {
    // location of test files
    private $filePath = './storage/app';

    public function setUp(): void 
    {
         parent::setUp();
         //Artisan::call('migrate:fresh --seed');
    }

    protected function tearDown(): void {
         //Artisan::call('migrate:rollback');
         parent::tearDown();
    }

    public function testLoad() {
        // calling load should return the list of files
        // in the flatfile directory under Storage
        // we are testing that the array is present and valid
        $response = (new TableController)->load();
        $this->assertIsArray($response->fltFleList);
    }

    public function testProcessEmptyFile() {
        // passing a empty file should throw an exception
        $path = './storage/app';
        $folder = 'flatfiles';
        $fileName = 'empty.csv';
        $collectionName = 'collection1';
        $tableName = 'testtable1';

        $this->testHelper->createCollectionWithTable($collectionName, $tableName);

        $emptyFile = $path.'/'.$folder.'/'.$fileName;
        touch($emptyFile);
        try {
          (new TableController)->process($tableName, $folder, $fileName);
        } catch (Exception $e) {
          $this->assertEquals("Cannot Import a Empty File.", $e->getMessage());
        }
        unlink($emptyFile);
        
        // drop testtable1
        \Schema::drop($tableName);

        // clear folder that was created with the collection
        File::deleteDirectory($this->filePath.'/'.$collectionName);

    }

  }
?>
