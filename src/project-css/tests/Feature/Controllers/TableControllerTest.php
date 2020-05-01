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

    public function setUp(): void {
          parent::setUp();

          // find admin and test user accounts
          $this->admin = User::where('name', '=', 'admin')->first();
          $this->user = User::where('name', '=', 'test')->first();

          // init helpers
          $this->tableHelper = new TableHelper;  
          $this->collectionHelper = new CollectionHelper;
    }

    protected function tearDown(): void {
           parent::tearDown();
    }

    public function testTableIndexView() {
         $this->actingAs($this->admin)
              ->get('/table')
              ->assertResponseStatus(200);
    }    

    public function testTableEditView() {
        // Create Test Collection        
        $collection = $this->createTestCollection('TestCollection', false);

        // Create Test Table
        $tableName = $this->createTestTable($collection);

        // get newly created table
        $table = Table::where('tblNme', $tableName)->first();

        $this->actingAs($this->admin)
             ->get('/table/edit/' . $table->id)
             ->assertResponseStatus(200);

        // drop test table
        Schema::dropIfExists($tableName);

        // Delete Test Collection
        $this->collectionHelper->deleteCollection($collection->clctnName);  
    }

    public function testTableUpdateView() {
        // Create Test Collection        
        $collection = $this->createTestCollection('TestCollection', false);

        // Create Test Table
        $tableName = $this->createTestTable($collection);

        // get newly created table
        $table = Table::where('tblNme', $tableName)->first();

        $this->actingAs($this->admin)
             ->visit('/table/edit/' . $table->id)
             ->assertResponseStatus(200)
             ->see('Step1: Confirm or Update Table Item(s)')
             ->see('Update Table')
             ->see('Name')
             ->type('NewTableName', 'name')
             ->press('Update');

        // fetch current table
        $table = Table::where('id', $table->id)->first();

        // check and see if table it was renamed
        $this->assertEquals($table->tblNme, 'NewTableName');

        // drop test table using original name
        Schema::dropIfExists($tableName);

        // drop test table using new name
        Schema::dropIfExists('NewTableName');

        // Delete Test Collection
        $this->collectionHelper->deleteCollection($collection->clctnName);  
    } 
    
    public function testTableUpdateViewWithUsedName() {
        // Create Test Collection        
        $collection = $this->createTestCollection('TestCollection', false);

        // Create Test Table
        $tableName1 = $this->createTestTable($collection);

        // Create Test Table 2
        $tableName2 = $this->createTestTable($collection);

        // get newly created tables
        $table = Table::where('tblNme', $tableName1)->first();

        $this->actingAs($this->admin)
             ->visit('/table/edit/' . $table->id)
             ->assertResponseStatus(200)
             ->see('Step1: Confirm or Update Table Item(s)')
             ->see('Update Table')
             ->see('Name')
             // Try Updating table1 with table 2's Name
             ->type($tableName2, 'name')
             ->press('Update');

        // fetch current table
        $table = Table::where('id', $table->id)->first();

        // verify rename failed and we still have our original name
        $this->assertEquals($table->tblNme, $tableName1);

        // drop test table using original name
        Schema::dropIfExists($tableName1);

        // drop test table using new name
        Schema::dropIfExists($tableName2);

        // Delete Test Collection
        $this->collectionHelper->deleteCollection($collection->clctnName);  
    }    

    public function testTableEditSchemaView() {
        // Create Test Collection        
        $collection = $this->createTestCollection('TestCollection', false);

        // Create Test Table
        $tableName = $this->createTestTable($collection);

        // get newly created table
        $table = Table::where('tblNme', $tableName)->first();

        $this->actingAs($this->admin)
             ->get('/table/edit/schema/' . $table->id)
             ->assertResponseStatus(200);

        // drop test table
        Schema::dropIfExists($tableName);

        // Delete Test Collection
        $this->collectionHelper->deleteCollection($collection->clctnName);  
    }    
    
//     public function testTableEditSchemaUpdate() {
//         // Create Test Collection        
//         $collection = $this->createTestCollection('TestCollection', false);

//         // Create Test Table
//         $tableName = $this->createTestTable($collection);

//         // get newly created table
//         $table = Table::where('tblNme', $tableName)->first();

//         $this->actingAs($this->admin)
//              ->get('/table/edit/schema/' . $table->id)
//              ->assertResponseStatus(200)
//              ->see('Column Name')
//              ->see('Default')
//              ->press('Submit');

//              //->select('Medium', 'col-0-size')
//              //->submitForm('Submit');


//         // drop test table
//         Schema::dropIfExists($tableName);

//         // Delete Test Collection
//         $this->collectionHelper->deleteCollection($collection->clctnName);  
//     }   


// Route::group([ 'prefix' => 'table' ], function() {
//     Route::get('edit/{curTable}', 'TableController@edit')->name('table.edit');
//     Route::post('update', 'TableController@update');

//     Route::get('edit/schema/{curTable}', 'TableController@editSchema')->name('table.edit.schema');
//     Route::post('update/schema', 'TableController@updateSchema');

//     Route::get('create', 'TableController@wizard');
//     Route::post('create/import', 'TableController@import');
//     Route::post('create/importcmsdis', 'TableController@importCMSDIS')->name('importcmsdis');
//     Route::post('create/select', 'TableController@select');
//     Route::post('create/selectCMSDIS', 'TableController@selectCMSDIS')->name('selectcmsdis');

//     // Forward route in case for error
//     Route::get('create/select', 'TableController@wizard');
//     Route::post('create/finalize', 'TableController@finalize');
//     Route::get('load', 'TableController@load');
//     Route::post('load/worker', 'TableController@worker');
//     Route::post('load/store', 'TableController@store');
//     Route::post('load/status', 'TableController@status');
//     Route::post('restrict', 'TableController@restrict');
    
    private function createTestTable($collection) {
        // set storage location
        $storageFolder = 'files/test';

        // set location of file
        $fileName = 'zillow.csv'; 
        
        // Create Test Table Name
        $tableName = 'test'.time();

        // Create New Name if tableName exists
        // Loop until we generate one that is not in use
        while(Schema::hasTable($tableName)) {
          $tableName = 'test'.time();
        } 

        // Create Table and Dispatch file Import
        $this->tableHelper->importFile($storageFolder, $fileName, $tableName, $collection->id, $collection->isCms);
        
        // return table name
        return ($tableName);
    }
    
    private function createTestCollection($name, $isCms) {
        // Create Collection Test Data Array
        $data = [
          'isCms' => $isCms,
          'name' => $name
        ];

        // Call collection helper create
        return($this->collectionHelper->create($data));         
    }


//  index 	
//  edit 	
//  update 	
//  editSchema 	
//  updateSchema 	
//  wizard 	
//  import 	
//  importCMSDIS 	
//  select 	
//  selectCMSDIS 	
//  finalize 	
//  getFiles 	
//  load 	
//  store 	
//  prepareLine 	
//  processLine 	
//  process 	
//  restrict 	





//     public function createTestTable($tblname, $path, $file) {
//            $this->testHelper->createCollection('collection1');

//            $this->actingAs($this->admin)
//                 ->visit('table/create')
//                 ->type($tblname, 'imprtTblNme')
//                 ->type('1', 'colID')
//                 ->attach($path . $file, 'fltFile')
//                 ->press('Import')
//                 ->assertResponseStatus(200)
//                 ->see('Edit Schema')
//                 ->submitForm('Submit', [ 'col-0-data' => 'string', 'col-0-size' => 'default',
//                                        'col-1-data' => 'string', 'col-1-size' => 'default',
//                                        'col-2-data' => 'string', 'col-2-size' => 'default',
//                                        'col-3-data' => 'string', 'col-3-size' => 'default',
//                                        'col-4-data' => 'string', 'col-4-size' => 'default',
//                                        'col-5-data' => 'string', 'col-5-size' => 'default',
//                                        'col-6-data' => 'string', 'col-6-size' => 'default',
//                                        'col-7-data' => 'string', 'col-7-size' => 'default',
//                                        'col-8-data' => 'string', 'col-8-size' => 'default',
//                                        'col-9-data' => 'string', 'col-9-size' => 'default',
//                                        'col-10-data' => 'string', 'col-10-size' => 'default',
//                                        'col-11-data' => 'string', 'col-11-size' => 'default',
//                                        'col-12-data' => 'string', 'col-12-size' => 'default',
//                                        'col-13-data' => 'string', 'col-13-size' => 'default',
//                                        'col-14-data' => 'string', 'col-14-size' => 'default',
//                                        'col-15-data' => 'string', 'col-15-size' => 'default',
//                                        'col-16-data' => 'integer', 'col-16-size' => 'medium',
//                                        'col-17-data' => 'string', 'col-17-size' => 'default',
//                                        'col-18-data' => 'string', 'col-18-size' => 'default',
//                                        'col-19-data' => 'integer', 'col-19-size' => 'big',
//                                        'col-20-data' => 'string', 'col-20-size' => 'big',
//                                        'col-21-data' => 'text', 'col-21-size' => 'default',
//                                        'col-22-data' => 'text', 'col-22-size' => 'medium',
//                                        'col-23-data' => 'string', 'col-23-size' => 'default',
//                                        'col-24-data' => 'string', 'col-24-size' => 'default',
//                                        'col-25-data' => 'string', 'col-25-size' => 'default',
//                                        'col-26-data' => 'string', 'col-26-size' => 'default',
//                                        'col-27-data' => 'string', 'col-27-size' => 'medium',
//                                        'col-28-data' => 'string', 'col-28-size' => 'big',
//                                        'col-29-data' => 'text', 'col-29-size' => 'big',
//                                        'col-30-data' => 'text', 'col-30-size' => 'big',
//                                        'col-31-data' => 'string', 'col-31-size' => 'default' ])
//                 ->assertResponseStatus(200)
//                 ->see('Load Data')
//                 ->press('Load Data')
//                 ->see('Table(s)')
//                 ->assertResponseStatus(200);
//     }

//     public function testNonAdminCannotCreateTable() {
//            // try to get to the user(s) page
//            $this->actingAs($this->user)
//                 ->get('table')
//                 //invalid user gets redirected
//                 ->assertResponseStatus(302);
//     }

//     public function testFileUploadAndTableCreate() {
//            $tblname = 'importtest'.mt_rand();
//            $path = './storage/app/files/test/';
//            $file = 'test.dat';

//            $this->createTestTable($tblname, $path, $file);

//            // test tables, files and folders that were created
//            $this->testHelper->cleanupTestTables([$file]);

//            // clear folder that was created with the collection
//            rmdir($this->filePath.'/collection1');
//     }

//     public function testCMSSelectFileAndTableCreate() {
//             $path = './storage/app/files/test/';
//             $file = '1A-random.tab';
//             $file2 = '1B-random.tab';

//             $this->testHelper->createCollection('collection1');

//             $storageFolder = 'flatfiles';

//             $fltFleAbsPth = './storage/app'.'/'.$storageFolder.'/';

//             copy($path.$file, $fltFleAbsPth.$file);
//             copy($path.$file2, $fltFleAbsPth.$file2);

//             $this->actingAs($this->admin)
//                  ->withoutMiddleware()
//                  ->call('POST', route('selectcmsdis'), ['colID2' => 1, 'cmsdisFiles2' => [$file, $file2]]);

//             $this->assertEquals(DB::table('tables')->count(), 2);

//             // test tables, files and folders that were created
//             $this->testHelper->cleanupTestTables([$file, $file2]);

//             // clear folder that was created with the collection
//             rmdir($this->filePath.'/collection1');
//     }

//     public function testCMSSelectInvalidFile() {
//             $path = './storage/app/files/test/';
//             $file = 'images.png';

//             $this->testHelper->createCollection('collection1');

//             $storageFolder = 'flatfiles';

//             $fltFleAbsPth = './storage/app'.'/'.$storageFolder.'/';

//             copy($path.$file, $fltFleAbsPth.$file);

//             $this->actingAs($this->admin)
//                  ->withoutMiddleware()
//                  ->call('POST', route('selectcmsdis'), ['colID2' => 1, 'cmsdisFiles2' => [$file]]);

//             $this->assertEquals(DB::table('tables')->count(), 0);

//             // test tables, files and folders that were created
//             $this->testHelper->cleanupTestTables([$file]);

//             // clear folder that was created with the collection
//             rmdir($this->filePath.'/collection1');
//     }

//     public function testInvalidFileTypeUpload() {
//             $tblname = 'importtest'.mt_rand();
//             $path = './storage/app/files/test/';
//             $file = 'images.png';

//             $this->testHelper->createCollection('collection1');

//             $this->actingAs($this->admin)
//                  ->visit('table/create')
//                  ->type($tblname, 'imprtTblNme')
//                  ->type('1', 'colID')
//                  ->attach($path.$file, 'fltFile')
//                  ->press('Import')
//                  ->assertResponseStatus(200)
//                  ->see('The flat file must be a file of type: text/plain.');

//             // test tables, files and folders that were created
//             $this->testHelper->cleanupTestTables([$file]);

//             // clear folder that was created with the collection
//             rmdir($this->filePath.'/collection1');
//     }

//     public function testFileExistsUpload() {
//             $tblname = 'importtest'.mt_rand();
//             $tblname2 = 'importtest'.mt_rand();
//             $path = './storage/app/files/test/';
//             $file = 'test.dat';

//             $this->createTestTable($tblname, $path, $file);

//             $this->actingAs($this->admin)
//                  ->visit('table/create')
//                  ->type($tblname2, 'imprtTblNme')
//                  ->type('1', 'colID')
//                  ->attach($path.$file, 'fltFile')
//                  ->press('Import')
//                  ->assertResponseStatus(200)
//                  ->see('File already exists. Please select the file or rename and re-upload.');

//             // test tables, files and folders that were created
//             $this->testHelper->cleanupTestTables([$file]);

//             // clear folder that was created with the collection
//             rmdir($this->filePath.'/collection1');
//     }

//     public function testCheckFlatFiles() {
//             // test to see if 'test.dat' is available
//             // using getFiles
//             $filesArray = (new TableController)->getFiles('.');
//             $this->assertContains('files/test/test.dat', $filesArray);
//     }

//     public function testSelect() {
//             $tblname = 'importtest'.mt_rand();
//             $tblname2 = 'importtest'.mt_rand();
//             $path = './storage/app/files/test/';
//             $file = 'test.dat';

//             $this->createTestTable($tblname, $path, $file);

//             $this->actingAs($this->admin)
//                  ->visit('table/create#slctPnlBdy')
//                  ->type($tblname2, 'slctTblNme')
//                  ->see('collection1')
//                  ->select($file, 'fltFile2')
//                  ->press('Select')
//                  ->see('Edit Schema')
//                  ->submitForm('Submit', [ 'col-0-data' => 'string', 'col-0-size' => 'default',
//                                           'col-1-data' => 'string', 'col-1-size' => 'default',
//                                           'col-2-data' => 'string', 'col-2-size' => 'default',
//                                           'col-3-data' => 'string', 'col-3-size' => 'default',
//                                           'col-4-data' => 'string', 'col-4-size' => 'default',
//                                           'col-5-data' => 'string', 'col-5-size' => 'default',
//                                           'col-6-data' => 'string', 'col-6-size' => 'default',
//                                           'col-7-data' => 'string', 'col-7-size' => 'default',
//                                           'col-8-data' => 'string', 'col-8-size' => 'default',
//                                           'col-9-data' => 'string', 'col-9-size' => 'default',
//                                           'col-10-data' => 'string', 'col-10-size' => 'default',
//                                           'col-11-data' => 'string', 'col-11-size' => 'default',
//                                           'col-12-data' => 'string', 'col-12-size' => 'default',
//                                           'col-13-data' => 'string', 'col-13-size' => 'default',
//                                           'col-14-data' => 'string', 'col-14-size' => 'default',
//                                           'col-15-data' => 'string', 'col-15-size' => 'default',
//                                           'col-16-data' => 'integer', 'col-16-size' => 'medium',
//                                           'col-17-data' => 'string', 'col-17-size' => 'default',
//                                           'col-18-data' => 'string', 'col-18-size' => 'default',
//                                           'col-19-data' => 'integer', 'col-19-size' => 'big',
//                                           'col-20-data' => 'string', 'col-20-size' => 'big',
//                                           'col-21-data' => 'text', 'col-21-size' => 'default',
//                                           'col-22-data' => 'text', 'col-22-size' => 'medium',
//                                           'col-23-data' => 'string', 'col-23-size' => 'default',
//                                           'col-24-data' => 'string', 'col-24-size' => 'default',
//                                           'col-25-data' => 'string', 'col-25-size' => 'default',
//                                           'col-26-data' => 'string', 'col-26-size' => 'default',
//                                           'col-27-data' => 'string', 'col-27-size' => 'medium',
//                                           'col-28-data' => 'string', 'col-28-size' => 'big',
//                                           'col-29-data' => 'text', 'col-29-size' => 'big',
//                                           'col-30-data' => 'text', 'col-30-size' => 'big',
//                                           'col-31-data' => 'string', 'col-31-size' => 'default' ])
//                  ->assertResponseStatus(200)
//                  ->see('Load Data')
//                  ->press('Load Data')
//                  ->see('Table(s)')
//                  ->assertResponseStatus(200);

//             // test tables, files and folders that were created
//             $this->testHelper->cleanupTestTables([$file]);

//             // clear folder that was created with the collection
//             rmdir($this->filePath.'/collection1');
//     }

//     public function testSelectAndCreateTableThenDisable() {
//             $tblname = 'importtest'.mt_rand();
//             $path = './storage/app/files/test/';
//             $file = 'test.dat';

//             $this->createTestTable($tblname, $path, $file);

//             // find table by searching on it's name
//             $table = Table::where('tblNme', '=', $tblname)->first();

//             // While using a admin account try to disable a table
//             $this->actingAs($this->admin)
//                  ->withoutMiddleware()
//                  ->post('table/restrict', [ 'id' => $table->id ]);
//             $table = Table::where('tblNme', '=', $tblname)->first();
//             $this->assertEquals('0', $table->hasAccess);

//             // test tables, files and folders that were created
//             $this->testHelper->cleanupTestTables([$file]);

//             // clear folder that was created with the collection
//             rmdir($this->filePath.'/collection1');
//     }

// //     public function testCMSImportFileUploadAndTableCreate() {
// //            $path = './storage/app/files/test/';
// //            $file = '1A-random.tab';
// //            $file2 = '1B-random.tab';

// //            $this->testHelper->createCollection('collection1');

// //            copy($path.$file, sys_get_temp_dir().'/'.$file);
// //            copy($path.$file2, sys_get_temp_dir().'/'.$file2);

// //            $files = [new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file, $file, 'application/octet-stream', filesize($path.$file), null, false),
// //                      new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file2, $file2, 'application/octet-stream', filesize($path.$file2), null, false)];

// //            $this->actingAs($this->admin)
// //                 ->withoutMiddleware()
// //                 ->call('POST', route('importcmsdis'), ['colID' => 1, 'cmsdisFiles' => $files]);

// //            $this->assertEquals(DB::table('tables')->count(), 2);

// //            // test tables, files and folders that were created
// //            $this->testHelper->cleanupTestTables([$file, $file2]);
// //     }

//     public function testCMSImportInvalidFileUploadAndTableCreate() {
//            $path = './storage/app/files/test/';
//            $file = 'images.png';

//            $this->testHelper->createCollection('collection1');

//            copy($path.$file, sys_get_temp_dir().'/'.$file);

//            $files = [new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file, $file, 'application/octet-stream', filesize($path.$file), null, false)];

//            $response = $this->actingAs($this->admin)
//                 ->withoutMiddleware()
//                 ->call('POST', route('importcmsdis'), ['colID' => 1, 'cmsdisFiles' => $files]);

//            $this->assertEquals(DB::table('tables')->count(), 0);

//            // test tables, files and folders that were created
//            $this->testHelper->cleanupTestTables([$file]);

//            // clear folder that was created with the collection
//            rmdir($this->filePath.'/collection1');
//     }

//     public function testCMSImportExistingFile() {
//            $path = './storage/app/files/test/';
//            $file = '1A-random.tab';

//            $this->testHelper->createCollection('collection1');

//            copy($path.$file, './storage/app/flatfiles/'.$file);

//            copy($path.$file, sys_get_temp_dir().'/'.$file);

//            $files = [new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file, $file, 'application/octet-stream', filesize($path.$file), null, false)];

//            $response = $this->actingAs($this->admin)
//                 ->withoutMiddleware()
//                 ->call('POST', route('importcmsdis'), ['colID' => 1, 'cmsdisFiles' => $files]);

//            $this->assertEquals(DB::table('tables')->count(), 0);

//            // test tables, files and folders that were created
//            $this->testHelper->cleanupTestTables([$file]);

//            // clear folder that was created with the collection
//            rmdir($this->filePath.'/collection1');
//     }

  }
