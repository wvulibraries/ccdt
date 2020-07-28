<?php
  # app/tests/controllers/CollectionControllerTest.php

  use App\Models\User;
  use App\Models\Collection;
  use App\Helpers\CollectionHelper;

  class CollectionControllerTest extends BrowserKitTestCase {
     private $adminEmail;
     private $adminPass;
     private $admin;
     private $user;

     // location of test files
     private $filePath = './storage/app';
     private $colName;   

     public function setUp(): void {
          parent::setUp();

          //admin credentials
          $this->adminEmail = "admin@admin.com";
          $this->adminPass = "testing";

          // find admin and test user accounts
          $this->admin = User::where('name', '=', 'admin')->first();
          $this->user = User::where('name', '=', 'test')->first();

          // Generate Collection Name
          $this->colName = $this->testHelper->generateCollectionName();
     }

     protected function tearDown(): void {
          // test tables, files and folders that were created
          $this->testHelper->cleanupTestTables();

          // Delete Test Collections
          $this->testHelper->deleteTestCollections();         

          parent::tearDown();
     }     

     public function testCreateCollection() {
          // Go to login page and enter credentials
          $this->visit('/login')
               ->type($this->adminEmail, 'email')
               ->type($this->adminPass, 'password')
               ->press('Login')
               ->seePageIs('/home');

          // Go to collection and create new collection
          $this->visit('/collection')
               ->see('Collection Name')
               ->type($this->colName, 'clctnName')
               ->press('Create')
               ->see('Create, import and manage collections here.')
               ->see($this->colName);
     }

     public function testNonAdminCannotCreateCollection() {
          // try to get to the user(s) page
          $this->actingAs($this->user)
               ->get('collection')
               //invalid user gets redirected
               ->assertResponseStatus(302);
     }

     public function testAdminCanCreateCollection() {
          // try to get to the user(s) page
          $this->actingAs($this->admin)
               ->get('/collection')
               ->assertResponseStatus(200)
               ->see('Create, import and manage collections here.');
     }

     public function testUploadFilesToCollection() {
          // Generate Test Collection
          $collection = $this->testHelper->createCollection($this->colName);

          // try to get to the collection page page
          $this->actingAs($this->admin)
               ->get('collection/upload/1')
               ->assertResponseStatus(200)
               ->see('Upload Linked File(s)');
     }

     // public function testCMSCreator() {
     //      // Generate Test Collection
     //      $collection = $this->testHelper->createCollection($this->colName);

     //      // try to get to the collection page page
     //      $this->actingAs($this->admin)
     //           ->get('/collection')
     //           ->see($this->colName);

     //      var_dump($collection);

     //      $this->actingAs($this->admin)
     //           ->get('/collection/show/'.$collection->id)     
     //           ->see('CMS View Creator')
     //           ->press('CMS View Creator')
     //           ->see('Please add tables to generate a CMS View');
     // }       

  }
