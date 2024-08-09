<?php
   # app/tests/Feature/Controllers/WizardControllerTest.php

   use Illuminate\Support\Facades\Storage;
   use App\Http\Controllers\TableController;
   use App\Http\Controllers\DataViewController;

   use App\Models\User;
   use App\Models\Table;

   class WizardControllerTest extends BrowserKitTestCase {
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

     protected function tearDown(): void {
          // test tables, files and folders that were created
          $this->testHelper->cleanupTestTables();

          // Delete Test Collections
          $this->testHelper->deleteTestCollections();   
          
          // Delete Test Files
          $this->testHelper->cleanupTestUploads(['mlb_players.csv', '1A-random.tab']);

          parent::tearDown();
     }       

     public function testImportFlatfilePage() {
         $this->actingAs($this->admin)
              ->visit('admin/wizard/flatfile')
              ->assertResponseStatus(200)
              ->see('Flatfile Import Wizard');           
     }
      
     public function testImportCMSPage() {
         $this->actingAs($this->admin)
              ->visit('admin/wizard/cms')
              ->assertResponseStatus(200)
              ->see('CMS Import Wizard');           
     }      

     public function testImportCollection() {
         // Generate Test Collection
         $this->testHelper->createCollection($this->colName);

         $this->actingAs($this->admin)
              ->visit('admin/wizard/import/collection/1')
              ->see('Select or Import')
              ->type($this->tableName, 'imprtTblNme')
              ->type('1', 'colID')
              ->attach('./storage/app/files/test/mlb_players.csv', 'fltFile')
              ->press('Import')
              ->assertResponseStatus(200)
              ->see('mlb_players.csv has been queued for import to '. $this->tableName .' table. It will be available shortly.');  
               
         // try uploading file 2nd time should return error
         $this->actingAs($this->admin)
              ->visit('admin/wizard/import/collection/1')
              ->see('Select or Import')
              ->type(mt_rand(), 'imprtTblNme')
              ->type('1', 'colID')
              ->attach('./storage/app/files/test/mlb_players.csv', 'fltFile')
              ->press('Import')
              ->assertResponseStatus(200)
              ->see('mlb_players.csv File already exists. Please select the file or rename and re-upload.');  
      }      

      public function testFlatFileFileUpload() {
         // Generate Test Collection
         $this->testHelper->createCollection($this->colName);

         $this->actingAs($this->admin)
              ->visit('admin/wizard/import/collection/1')
              ->see('Import Flatfile Collection')
              ->type($this->tableName, 'imprtTblNme')
              ->type('1', 'colID')
              ->attach('./storage/app/files/test/mlb_players.csv', 'fltFile')
              ->press('Import')
              ->assertResponseStatus(200)
              ->see('mlb_players.csv has been queued for import to')
              ->see('table. It will be available shortly.');               
      }

      public function testCMSFileFileUpload() {
         // Generate Test Collection
         $this->testHelper->createCollection($this->colName, 1, true);

         $this->actingAs($this->admin)
              ->visit('admin/wizard/import/collection/1')
              ->see('Import CMS Collection')
              ->type('1', 'colID')
              ->attach('./storage/app/files/test/1A-random.tab', 'cmsFile')
              ->press('Import')
              ->assertResponseStatus(200)
              ->see('1A-random.tab has been queued for import to')
              ->see('table. It will be available shortly.');               
      }

   }
