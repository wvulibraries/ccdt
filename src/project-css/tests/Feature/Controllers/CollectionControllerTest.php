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
     private $tableName;  

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

     public function testUserShowInvaidCollection() {
          // try to get collectino page on invalid collection
          $this->actingAs($this->user)
               ->get('/collection/1')
               ->assertResponseStatus(404)
               ->see('We could not find the page you were looking for.');
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
     public function testUploadFilesToMissingCollection() {
          // try to get to the collection page page
          $this->actingAs($this->admin)
               ->get('collection/upload/1')
               ->assertResponseStatus(404);
     }

     // Note testing CMS Creator here isn't working correctly
     // Feature not implemented yet
     public function testCMSCreator() {
        // Generate Test Collection
        $collection = $this->testHelper->createCollection($this->colName, 1, true);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection);

        // Create 2nd Test Table
        $tableName2 = $this->testHelper->createTestTable($collection);        

        // try to get to the collection page page
        $this->actingAs($this->admin)
             ->get('/collection')
             ->see($this->colName);

        $this->actingAs($this->admin)
             ->visit('/collection/show/'.$collection->id)     
             ->see('CMS View Creator')
             ->click('CMS View Creator')
             ->see('Please add tables to generate a CMS View');
     }      

     // Note testing CMS Creator here isn't working correctly
     // Feature not implemented yet
     public function testCMSCreatorOnNonCMSCollection() {
        // Generate Test Collection
        $collection = $this->testHelper->createCollection($this->colName);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection);

        // try to get to the collection page page
        $this->actingAs($this->admin)
             ->get('/collection')
             ->see($this->colName);

        $this->actingAs($this->admin)
             ->visit('/collection/show/'.$collection->id)     
             ->see('CMS View Creator')
             ->click('CMS View Creator')
             ->see('Current Collection is Not a CMS Database');
     }       

  }
