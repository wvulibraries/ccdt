<?php
  # app/tests/controllers/DataViewControllerTest.php
  use \Illuminate\Session\SessionManager;
  use App\Http\Controllers\DataViewController;
  use App\Models\User;
  use App\Models\Collection;
  use App\Models\Table;
  use App\Libraries\TestHelper;

  class DataViewControllerTest extends BrowserKitTestCase {

    private $admin;
    private $user;
    private $collection;

    /**
    * @var \Illuminate\Session\SessionManager
    */
    protected $manager;

    public function setUp(): void {
           parent::setUp();
           Artisan::call('migrate');
           Artisan::call('db:seed');
           Session::setDefaultDriver('array');
           $this->manager = app('session');

           // find admin and test user accounts
           $this->admin = User::where('name', '=', 'admin')->first();
           $this->user = User::where('name', '=', 'test')->first();
    }

    protected function tearDown(): void {
           Artisan::call('migrate:reset');
           parent::tearDown();
    }

    public function createTestTable($tblname, $path, $file) {
            // create a test collection
            $this->collection = (new TestHelper)->createCollection('collection1');

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
    }

    public function cleanup($tblname, $file) {
           // cleanup remove directory for the test table
           Storage::deleteDirectory($tblname);

           // cleanup remove header_only.csv from upload folder
           Storage::delete('/flatfiles/'.$file);

           // drop table after Testing
           Schema::drop($tblname);
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
                ->see('3');

           $this->actingAs($this->admin)
                ->visit('data/1')
                ->see($tblname.' Records')
                ->visit('data/1/1')
                ->assertResponseStatus(200)
                ->see('Doe');

           $this->cleanup($tblname, $file);
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

           $this->cleanup($tblname, $file);
    }

    public function testSearchNoResults() {
           $tblname = 'importtest'.mt_rand();
           $path = './storage/app/files/test/';
           $file = 'test.dat';

           $this->createTestTable($tblname, $path, $file);

           //search for a name this will go to the fulltext search
           $this->actingAs($this->admin)
                ->visit('data/1')
                ->type('NoResults', 'search')
                ->press('Search')
                ->assertResponseStatus(200)
                ->see('Search Yeilded No Results');

           $this->cleanup($tblname, $file);
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

           $this->cleanup($tblname, $file);
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

            $this->cleanup($tblname, $file);
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

           //while table is disabled try to force a Search
           //test bypasses middleware since we are calling
           //the controller directly and bypassing
           //the middleware that checks for access
           $request = new \Illuminate\Http\Request();
           $request->setLaravelSession($this->manager->driver());
           $request->session(['search' => "-------"]);
           $response = (new DataViewController)->search($request, "1", "1");
           $errors = $response->errors->all();
           $this->assertEquals($errors[0], "Search Yeilded No Results");

           //While using a admin account try to enable a collection
           $this->post('collection/enable', [ 'id' => $this->collection->id, 'clctnName' => $this->collection->clctnName ])
                ->visit('data/1')
                ->assertResponseStatus(200)
                ->see($tblname);

           $this->cleanup($tblname, $file);
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

           $this->cleanup($tblname, $file);
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
                ->see('0');

           // visit a table with no records
           $this->visit('data/1')
                ->assertResponseStatus(200)
                ->see('Table does not have any records.');

           $this->cleanup($tblname, $file);
    }

    public function testNullShow() {
           // test to see if passing null for both table id and record produces a error response
           $response = (new DataViewController)->show(null, null);
           $errors = $response->getSession()->get('errors', new Illuminate\Support\MessageBag)->all();
           $this->assertEquals($errors[0], "Invalid Record ID");
    }

  }
?>
