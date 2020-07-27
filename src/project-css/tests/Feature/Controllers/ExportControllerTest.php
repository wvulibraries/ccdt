<?php
   # app/tests/Feature/Controllers/ExportControllerTest.php

   use App\Models\User;

   class ExportControllerTest extends BrowserKitTestCase {
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

          parent::tearDown();
     }       

      public function testExportPage() {
         $this->actingAs($this->admin)
              ->visit('admin/wizard/export')
              ->assertResponseStatus(200)
              ->see('Export Tables and Collections');           
      }

      public function testExportTable() {
         $this->actingAs($this->admin)
              ->visit('admin/wizard/export/table')
              ->assertResponseStatus(200)
              ->see('Export Table Wizard');           
      }

      public function testExportCollection() {
         $this->actingAs($this->admin)
              ->visit('admin/wizard/export/collection')
              ->assertResponseStatus(200)
              ->see('Export Collection Wizard');           
      }      
   }
