<?php
  # app/tests/Unit/controllers/TableControllerUnitTest.php

  use Illuminate\Support\Facades\Storage;
  use App\Adapters\ImportAdapter;

  class ImportAdapterUnitTest extends BrowserKitTestCase
  {
    // location of test files
    private $filePath = './storage/app';

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
          (new ImportAdapter)->process($tableName, $folder, $fileName);
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
