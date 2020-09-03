<?php
   # app/tests/controllers/TableControllerTest.php

    use Illuminate\Support\Facades\Storage;
    use App\Http\Controllers\TableController;
    use App\Http\Controllers\DataViewController;
    use App\Helpers\CollectionHelper;
    use App\Helpers\TableHelper;
    use App\Models\User;
    use App\Models\Table;

    class TableControllerTest extends BrowserKitTestCase {
     private $admin;
     private $user;
     private $collection;
     public $tableHelper;
     public $collectionHelper;

     // location of test files
     private $filePath = './storage/app';

     private $colName;
     private $colName2;
     private $tableName;       

     public function setUp(): void {
          parent::setUp();

          // find admin and test user accounts
          $this->admin = User::where('name', '=', 'admin')->first();
          $this->user = User::where('name', '=', 'test')->first();

          // init helpers
          $this->tableHelper = new TableHelper;  
          $this->collectionHelper = new CollectionHelper;

          // Generate Collection Name
          $this->colName = $this->testHelper->generateCollectionName();
          $this->colName2 = $this->colName+1;

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

     public function testTableIndexView() {
         $this->actingAs($this->admin)
              ->get('/table')
              ->assertResponseStatus(200);
     }    

     public function testTableEditView() {
        // Create Test Collection        
        $collection = $this->testHelper->createCollection($this->colName, 1, false);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection);

        // get newly created table
        $table = Table::where('tblNme', $tableName)->first();

        $this->actingAs($this->admin)
             ->get('/table/edit/' . $table->id)
             ->assertResponseStatus(200);
     }

     public function testTableUpdateView() {
        // Create Test Collection        
        $collection = $this->testHelper->createCollection($this->colName, 1, false);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection);

        // get newly created table
        $table = Table::where('tblNme', $tableName)->first();

        $this->actingAs($this->admin)
             ->visit('/table/edit/' . $table->id)
             ->assertResponseStatus(200)
             ->see('Confirm or Update Table Item(s)')
             ->see('Update Table')
             ->see('Name')
             ->type($this->tableName, 'name')
             ->press('Update');

        // fetch current table
        $table = Table::where('id', $table->id)->first();

        // check and see if table it was renamed
        $this->assertEquals($table->tblNme, $this->tableName);
     } 
    
     public function testTableUpdateViewWithUsedName() {
        // Create Test Collection        
        $collection = $this->testHelper->createCollection($this->colName, 1, false);

        // Create Test Table
        $tableName1 = $this->testHelper->createTestTable($collection);

        // Create Test Table 2
        $tableName2 = $this->testHelper->createTestTable($collection);

        // get newly created tables
        $table = Table::where('tblNme', $tableName1)->first();

        $this->actingAs($this->admin)
             ->visit('/table/edit/' . $table->id)
             ->assertResponseStatus(200)
             ->see('Confirm or Update Table Item(s)')
             ->see('Update Table')
             ->see('Name')
             // Try Updating table1 with table 2's Name
             ->type($tableName2, 'name')
             ->press('Update');

        // fetch current table
        $table = Table::where('id', $table->id)->first();

        // verify rename failed and we still have our original name
        $this->assertEquals($table->tblNme, $tableName1);
     }    

     public function testTableUpdateCollection() {
        // Create Test Collection        
        $collection = $this->testHelper->createCollection($this->colName, 1, false);
        $collection2 = $this->testHelper->createCollection($this->colName2, 1, false);        

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection);

        // get newly created tables
        $table = Table::where('tblNme', $tableName)->first();

        $this->actingAs($this->admin)
             ->visit('/table/edit/' . $table->id)
             ->assertResponseStatus(200)
             ->see('Confirm or Update Table Item(s)')
             ->see('Update Table')
             ->see('Select Collection')
             ->select($collection2->id, 'colID')
             ->press('Update');

        // fetch current table
        $table = Table::where('id', $table->id)->first();

        // verify rename failed and we still have our original name
        $this->assertEquals($table->collection_id, $collection2->id);
     }      

    public function testTableEditSchemaView() {
        // Create Test Collection        
        $collection = $this->testHelper->createCollection($this->colName, 1, false);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection);

        // get newly created table
        $table = Table::where('tblNme', $tableName)->first();

        $this->actingAs($this->admin)
             ->get('/table/edit/schema/' . $table->id)
             ->assertResponseStatus(200);
     }    
    
     public function testTableEditSchemaUpdate() {
        // Create Test Collection        
        $collection = $this->testHelper->createCollection($this->colName, 1, false);

        // Create Test Table
        $tableName = $this->testHelper->createTestTable($collection);

        // get newly created table
        $table = Table::where('tblNme', $tableName)->first();

        $this->actingAs($this->admin)
             ->visit('/table/edit/schema/' . $table->id)
             ->assertResponseStatus(200)
             ->see('Edit Table Schema')
             ->submitForm('Submit', [ 'col-0-name' => 'index', 'col-0-data' => 'integer', 'col-0-size' => 'big',
                                   'col-1-name' => 'living_space_sq_ft', 'col-1-data' => 'integer', 'col-1-size' => 'big',
                                   'col-2-name' => 'beds', 'col-2-data' => 'integer', 'col-2-size' => 'big',
                                   'col-3-name' => 'baths', 'col-3-data' => 'integer', 'col-3-size' => 'big',
                                   'col-4-name' => 'zip', 'col-4-data' => 'integer', 'col-4-size' => 'big',
                                   'col-5-name' => 'year', 'col-5-data' => 'string', 'col-5-size' => 'default',
                                   'col-6-name' => 'list_price', 'col-6-data' => 'text', 'col-6-size' => 'default'])             
             ->assertResponseStatus(200);

        $this->actingAs($this->admin)
             ->visit('/table/edit/schema/' . $table->id)
             ->assertResponseStatus(200)
             ->see('string'); 
     }   

     public function testAdminCanCreateTable() {
          // Create Test Collection        
          $collection = $this->testHelper->createCollection($this->colName, 1, false);

          // try to get to the user(s) page
          $this->actingAs($this->admin)
               ->get('table/create')
               ->see('Add Tables');
     }
         
     public function testNonAdminCannotCreateTable() {
          // try to get to the user(s) page
          $this->actingAs($this->user)
               ->get('table/create')
               ->assertResponseStatus(302);
     }

     public function testCheckFlatFiles() {
            // test to see if 'test.dat' is available
            // using getFiles
            $filesArray = (new TableController)->getFiles('.');
            $this->assertContains('files/test/test.dat', $filesArray);
     } 

     public function testTableRestrict() {
          $this->testHelper->createCollectionWithTableAndRecords($this->colName, $this->tableName);

          // find table by searching on it's name
          $table = Table::where('tblNme', '=', $this->tableName)->first();

          // While using a admin account try to disable a table
          $this->actingAs($this->admin)
               ->withoutMiddleware()
               ->post('table/restrict', [ 'id' => $table->id ]);
          $table = Table::where('tblNme', '=', $this->tableName)->first();
          $this->assertEquals('0', $table->hasAccess);          
     }

  }
