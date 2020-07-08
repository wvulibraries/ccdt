<?php
   # app/tests/Unit/Controllers/WizardControllerTest.php

   use Illuminate\Support\Facades\Storage;
   use App\Http\Controllers\TableController;
   use App\Http\Controllers\DataViewController;

   use App\Models\User;
   use App\Models\Table;

   class WizardControllerUnitTest extends BrowserKitTestCase {
      private $admin;
      private $user;

      private $colName;
      private $tableName;      

      // location of test files
      private $filePath = './storage/app';

      public function setUp(): void {
         parent::setUp();

         // find admin and test user accounts
         $this->admin = User::where('name', '=', 'admin')->first();
         $this->user = User::where('name', '=', 'test')->first();

         // Generate Collection Name
         $this->colName = $this->testHelper->generateCollectionName();

         // Generate Table Name
         $this->tableName = $this->testHelper->createTableName();         
      }

//     public function testCMSImportFileUploadAndTableCreate() {
//            $path = './storage/app/files/test/';
//            $file = '1A-random.tab';
//            $file2 = '1B-random.tab';

//            $this->testHelper->createCollection('collection1');

//            copy($path.$file, sys_get_temp_dir().'/'.$file);
//            copy($path.$file2, sys_get_temp_dir().'/'.$file2);

//            $files = [new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file, $file, 'application/octet-stream', filesize($path.$file), null, false),
//                      new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file2, $file2, 'application/octet-stream', filesize($path.$file2), null, false)];

//            $this->actingAs($this->admin)
//                 ->withoutMiddleware()
//                 ->call('POST', route('wizard.cms.upload'), ['colID' => 1, 'cmsdisFiles' => $files]);

//            $this->assertEquals(DB::table('tables')->count(), 2);

//            // test tables, files and folders that were created
//            $this->testHelper->cleanupTestTablesAndFiles([$file, $file2]);
//     }

      public function testCMSImportInvalidFileUploadAndTableCreate() {
         $path = './storage/app/files/test/';
         $file = 'images.png';

         $this->testHelper->createCollection($this->colName);

         copy($path.$file, sys_get_temp_dir().'/'.$file);

         $file = new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file, $file, 'application/octet-stream', filesize($path.$file), null, false);

         $response = $this->actingAs($this->admin)
                          ->withoutMiddleware()
                          ->call('POST', route('wizard.cms.upload'), ['colID' => 1, 'cmsFile2' => $file]);

         // validation error is thrown due to invalid file type
         $this->assertSessionHasErrors();

         // Delete Test Collection
         $this->testHelper->deleteCollection($this->colName);
      }       

      public function testFlatFileSelect() {
         $path = './storage/app/files/test/';
         $file = 'zillow.csv';

         $this->testHelper->createCollection($this->colName, false);

         $storageFolder = 'flatfiles';

         $fltFleAbsPth = './storage/app'.'/'.$storageFolder.'/';

         copy($path.$file, $fltFleAbsPth.$file);

         $this->actingAs($this->admin)
              ->withoutMiddleware()
              ->call('POST', route('wizard.flatfile.select'), ['colID2' => 1, 'slctTblNme' => $this->tableName, 'fltFile2' => $file]);

         $this->assertEquals(DB::table('tables')->count(), 1);

         // test tables, files and folders that were created
         $this->testHelper->cleanupTestTablesAndFiles([$file]);

         // Delete Test Collection
         $this->testHelper->deleteCollection($this->colName);           
      }

      public function testCMSSelect() {
         $path = './storage/app/files/test/';
         $file = '1A-random.tab';

         $this->testHelper->createCollection($this->colName);

         $storageFolder = 'flatfiles';

         $fltFleAbsPth = './storage/app'.'/'.$storageFolder.'/';

         copy($path.$file, $fltFleAbsPth.$file);

         $this->actingAs($this->admin)
              ->withoutMiddleware()
              ->call('POST', route('wizard.cms.select'), ['colID2' => 1, 'cmsFile2' => $file]);

         $this->assertEquals(DB::table('tables')->count(), 1);

         // test tables, files and folders that were created
         $this->testHelper->cleanupTestTablesAndFiles([$file]);

         // Delete Test Collection
         $this->testHelper->deleteCollection($this->colName);           
      }      
   
  }
