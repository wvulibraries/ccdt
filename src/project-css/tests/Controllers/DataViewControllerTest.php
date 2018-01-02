<?php
  # app/tests/controllers/DataViewControllerTest.php

  use App\Http\Controllers\DataViewController;

  class DataViewControllerTest extends TestCase {

    private $admin;
    private $user;

    public function setUp() {
           parent::setUp();
           Artisan::call('migrate');
           Artisan::call('db:seed');

           // find admin and test user accounts
           $this->admin = App\User::where('name', '=', 'admin')->first();
           $this->user = App\User::where('name', '=', 'test')->first();
    }

    protected function tearDown() {
           Artisan::call('migrate:reset');
           parent::tearDown();
    }

    public function createCollection($name) {
           $this->collection = factory(App\Collection::class)->create([
                'clctnName' => $name,
           ]);
    }

    public function createTestTable($tblname, $path, $file) {
            $this->createCollection('collection1');

            $this->actingAs($this->admin)
                 ->visit('table/create')
                 ->type($tblname, 'imprtTblNme')
                 ->type('1', 'colID')
                 ->attach($path . $file, 'fltFile')
                 ->press('Import')
                 ->assertResponseStatus(200)
                 ->see('Edit Schema')
                 ->submitForm('Submit', [ 'col-0-data' => 'string', 'col-0-size' => 'default',
                                         'col-1-data' => 'string', 'col-1-size' => 'default',
                                         'col-2-data' => 'string', 'col-2-size' => 'default',
                                         'col-3-data' => 'string', 'col-3-size' => 'default',
                                         'col-4-data' => 'string', 'col-4-size' => 'default',
                                         'col-5-data' => 'string', 'col-5-size' => 'default',
                                         'col-6-data' => 'string', 'col-6-size' => 'default',
                                         'col-7-data' => 'string', 'col-7-size' => 'default',
                                         'col-8-data' => 'string', 'col-8-size' => 'default',
                                         'col-9-data' => 'string', 'col-9-size' => 'default',
                                         'col-10-data' => 'string', 'col-10-size' => 'default',
                                         'col-11-data' => 'string', 'col-11-size' => 'default',
                                         'col-12-data' => 'string', 'col-12-size' => 'default',
                                         'col-13-data' => 'string', 'col-13-size' => 'default',
                                         'col-14-data' => 'string', 'col-14-size' => 'default',
                                         'col-15-data' => 'string', 'col-15-size' => 'default',
                                         'col-16-data' => 'string', 'col-16-size' => 'default',
                                         'col-17-data' => 'string', 'col-17-size' => 'default',
                                         'col-18-data' => 'string', 'col-18-size' => 'default',
                                         'col-19-data' => 'string', 'col-19-size' => 'default',
                                         'col-20-data' => 'string', 'col-20-size' => 'big',
                                         'col-21-data' => 'text', 'col-21-size' => 'default',
                                         'col-22-data' => 'text', 'col-22-size' => 'default',
                                         'col-23-data' => 'string', 'col-23-size' => 'default',
                                         'col-24-data' => 'string', 'col-24-size' => 'default',
                                         'col-25-data' => 'string', 'col-25-size' => 'default',
                                         'col-26-data' => 'string', 'col-26-size' => 'default',
                                         'col-27-data' => 'string', 'col-27-size' => 'default',
                                         'col-28-data' => 'string', 'col-28-size' => 'big',
                                         'col-29-data' => 'text', 'col-29-size' => 'default',
                                         'col-30-data' => 'text', 'col-30-size' => 'default',
                                         'col-31-data' => 'string', 'col-31-size' => 'default' ])
                 ->assertResponseStatus(200)
                 ->see('Load Data')
                 ->press('Load Data')
                 ->see('Table(s)')
                 ->assertResponseStatus(200);

                 // test isValidTable
                 $this->assertTrue((new DataViewController)->isValidTable('1'));

                 // cleanup remove sample.dat from upload folder
                 Storage::delete('/flatfiles/' . $file);
    }

    public function testIndexWithInvalidTable() {
           //try to import a table without a collection
           $this->actingAs($this->admin)
                ->visit('data/1')
                ->see("Table id is invalid");

           // test using non-numeric table id
           $this->actingAs($this->admin)
                ->visit('data/idontexist')
                ->see("Table id is invalid");
    }

    public function testImportWithRecords() {
           $tblname = 'importtest'.mt_rand();
           $path = './storage/app/files/test/';
           $file = 'test.dat';

           $this->createTestTable($tblname, $path, $file);

           $this->actingAs($this->admin)
                ->visit('table')
                ->assertResponseStatus(200)
                ->see('3 Records');

           $this->actingAs($this->admin)
                ->visit('data/1')
                ->see($tblname.' Records')
                ->visit('data/1/1')
                ->assertResponseStatus(200)
                ->see('Doe');

           // cleanup remove directory for the test table
           Storage::deleteDirectory($tblname);

           // cleanup remove header_only.csv from upload folder
           Storage::delete('/flatfiles/'.$file);

           // drop table after Testing
           Schema::drop($tblname);
    }

    public function testSearch() {
           $tblname = 'importtest'.mt_rand();
           $path = './storage/app/files/test/';
           $file = 'test.dat';

           $this->createTestTable($tblname, $path, $file);

           //search for a name this will go to the fulltext search
           $this->actingAs($this->admin)
                ->visit('data/1')
                ->type('Doe', 'search')
                ->press('Search')
                ->assertResponseStatus(200)
                ->see('John');

           // cleanup remove directory for the test table
           Storage::deleteDirectory($tblname);

           // cleanup remove header_only.csv from upload folder
           Storage::delete('/flatfiles/'.$file);

           // drop table after Testing
           Schema::drop($tblname);
    }

    public function testInvalidId() {
           $tblname = 'importtest'.mt_rand();
           $path = './storage/app/files/test/';
           $file = 'test.dat';

           $this->createTestTable($tblname, $path, $file);

           // view specific record with a invalid id
           // should produce error messsage that no results
           $this->actingAs($this->admin)
                ->visit('data/1/2000')
                ->assertResponseStatus(200)
                ->see('Search Yeilded No Results');

           // cleanup remove directory for the test table
           Storage::deleteDirectory($tblname);

           // cleanup remove header_only.csv from upload folder
           Storage::delete('/flatfiles/'.$file);

           // drop table after Testing
           Schema::drop($tblname);
    }

    public function uploadFileToDatabaseAndView($upload) {
            $tblname = 'importtest'.mt_rand();
            $path = './storage/app/files/test/';
            $file = 'test.dat';

            $this->createTestTable($tblname, $path, $file);

            $this->actingAs($this->admin)
                 ->visit('upload/1')
                 ->assertResponseStatus(200)
                 ->see('Upload files to '.$tblname.' Table')
                 ->type('test', 'upFldNme')
                 ->attach(array($path.$upload), 'attachments[]')
                 ->press('Upload')
                 ->assertResponseStatus(200)
                 ->see('Upload files to '.$tblname.' Table')
                 ->assertFileExists(storage_path('app/'.$tblname.'/test/'.$upload));

            $this->visit('data/1/1/view'.'/test/'.$upload)
                 ->assertResponseStatus(200);

            // cleanup remove directory for the test table
            Storage::deleteDirectory($tblname);

            // cleanup remove file from upload folder
            Storage::delete('/flatfiles/'.$file);

            // drop table after Testing
            Schema::drop($tblname);
    }

    public function testUploadAndViewUploadedTxtFile() {
           $this->uploadFileToDatabaseAndView('test_upload.txt');
    }

    public function testUploadAndViewUploadedDocFile() {
           $this->uploadFileToDatabaseAndView('test_upload.doc');
    }

    public function testUploadAndViewUploadedDocxFile() {
           $this->uploadFileToDatabaseAndView('test_upload.docx');
    }

    public function testUploadAndViewUploadedPdfFile() {
           $this->uploadFileToDatabaseAndView('test_upload.pdf');
    }

    public function testUploadAndViewUploadedPngFile() {
           $this->uploadFileToDatabaseAndView('images.png');
    }

    public function testViewDisabledCollection() {
           $tblname = 'importtest'.mt_rand();
           $path = './storage/app/files/test/';
           $file = 'test.dat';

           $this->createTestTable($tblname, $path, $file);

           //create empty file to test view file
           $emptyFile = 'empty.csv';
           $filePath = './storage/app/'.$tblname.'/test';
           mkdir($filePath);
           touch($filePath.'/'.$emptyFile);

           // While using a admin account try to disable a collection
           $this->actingAs($this->admin)
                ->post('collection/disable', [ 'id' => $this->collection->id, 'clctnName' => $this->collection->clctnName ])
                // try to visit the disabled table
                ->visit('data/1')
                ->assertResponseStatus(200)
                ->see('Table is disabled');

          // try to view a record in a disabled table
          $this->visit('data/1/1')
                ->assertResponseStatus(200)
                ->see('Table is disabled');

           //while table is disabled try to view a file
           $this->visit('data/1/1/view'.'/test/'.$emptyFile)
                ->assertResponseStatus(200)
                ->see('Table is disabled');

           //While using a admin account try to enable a collection
           $this->post('collection/enable', [ 'id' => $this->collection->id, 'clctnName' => $this->collection->clctnName ])
                ->visit('data/1')
                ->assertResponseStatus(200)
                ->see($tblname);

           // cleanup remove directory for the test table
           Storage::deleteDirectory($tblname);

           // cleanup remove header_only.csv from upload folder
           Storage::delete('/flatfiles/'.$file);

           // drop table after Testing
           Schema::drop($tblname);
    }

    public function testViewInvalidRecord() {
           $tblname = 'importtest'.mt_rand();
           $path = './storage/app/files/test/';
           $file = 'test.dat';

           $this->createTestTable($tblname, $path, $file);

           // view specific record with a invalid id
           // should produce error messsage that no results
           $this->actingAs($this->admin)
                ->visit('data/1/2000')
                ->assertResponseStatus(200)
                ->see('Search Yeilded No Results');

           // cleanup remove directory for the test table
           Storage::deleteDirectory($tblname);

           // cleanup remove header_only.csv from upload folder
           Storage::delete('/flatfiles/'.$file);

           // drop table after Testing
           Schema::drop($tblname);
    }

    public function testImportWithNoRecords() {
           $tblname = 'importtest'.mt_rand();
           $path = './storage/app/files/test/';
           $file = 'header_only.dat';

           // make sure file isn't already their
           Storage::delete('/flatfiles/'.$file);

           $this->createTestTable($tblname, $path, $file);

           $this->actingAs($this->admin)
                ->visit('table')
                ->assertResponseStatus(200)
                ->see('0 Records');

           // visit a table with no records
           $this->visit('data/1')
                ->assertResponseStatus(200)
                ->see('Table does not have any records.');

           // cleanup remove directory for the test table
           Storage::deleteDirectory($tblname);

           // cleanup remove header_only.csv from upload folder
           Storage::delete('/flatfiles/'.$file);

           // drop table after Testing
           Schema::drop($tblname);
    }

    public function testInvalidTableId() {
           // test to see if table id 99 is available
           // test should fail
           $this->assertFalse((new DataViewController)->isValidTable('99'));
    }

  }
?>
