<?php
  # app/tests/controllers/DataViewControllerTest.php
  use \Illuminate\Session\SessionManager;
  use App\Http\Controllers\DataViewController;
  use App\Models\User;
  use App\Models\Collection;
  use App\Models\Table;
  use Illuminate\Foundation\Testing\WithoutMiddleware;

  class DataViewControllerTest extends BrowserKitTestCase {

    private $admin;
    private $user;
    private $collection;
    private $table;
    private $tblname;
    private $path;
    private $file;

    /**
    * @var \Illuminate\Session\SessionManager
    */
    protected $manager;

    public function setUp(): void {
           parent::setUp();
           Artisan::call('migrate:refresh --seed');
           Session::setDefaultDriver('array');
           $this->manager = app('session');

           // find admin and test user accounts
           $this->admin = User::where('name', '=', 'admin')->first();
           if($this->admin == null){
              $this->fail('No admin user present');
           }

           $this->user = User::where('name', '=', 'test')->first();
           if($this->user == null){
              $this->fail('No user present');
           }
    }

    public function cleanup() {
           if ($this->file != NULL) {
              // cleanup remove header_only.csv from upload folder
              Storage::delete('/flatfiles/'.$this->file);
           } 

           if ($this->tblname != NULL) {
              // cleanup remove directory for the test table
              Storage::deleteDirectory($this->tblname);

              // drop table after Testing
              Schema::drop($this->tblname);
           }
    }

    protected function tearDown(): void {
           $this->cleanup($this->tblname, $this->file);

           Artisan::call('migrate:rollback');
           parent::tearDown();
    }

    public function createTestTable($path = './storage/app/files/test/', $file = 'test.dat') {
            $this->tblname = 'importtest'.mt_rand();
            $this->path = $path;
            $this->file = $file;

            // create a test collection
            $this->collection = (new TestHelper)->createCollection('collection1');
            $this->actingAs($this->admin)
                 ->visit('table/create')
                 ->see('collection1')
                 ->type($this->tblname, 'imprtTblNme')
                 ->type('1', 'colID')
                 ->attach($this->path . $this->file, 'fltFile')
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

              // tests running too fast let job queue import
              sleep(5);  
    }

    public function testInvalidId() {
           $this->createTestTable();

           // view specific record with a invalid id
           // should produce error messsage that no results
           $this->actingAs($this->admin)
                ->visit('data/1/2000')
                ->assertResponseStatus(200)
                ->see('Search Yeilded No Results');
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
           $this->createTestTable();

           // verify we can see table in the table list
           $this->actingAs($this->admin)
                ->visit('table')
                ->assertResponseStatus(200)
                ->see($this->tblname);

           // verify we can get to table
           $this->actingAs($this->admin)
                ->visit('data/1')
                ->assertResponseStatus(200);

           $this->actingAs($this->admin)
                ->visit('data/1/1')
                ->assertResponseStatus(200);

           $this->actingAs($this->admin)
                ->visit('data/1')
                ->see('Doe');       
    }

//     public function testSearch() {
//            $this->createTestTable();

//            //search for a name this will go to the fulltext search
//            $this->actingAs($this->admin)
//                 ->visit('data/1')
//                 ->type('Doe', 'search')
//                 ->press('Search')
//                 ->assertResponseStatus(200)
//                 ->see('John');
//     }

//     public function testSearchNoResults() {
//            $this->createTestTable();

//            //search for a name this will go to the fulltext search
//            $this->actingAs($this->admin)
//                 ->visit('data/1')
//                 ->see('search')
//                 ->type('NoResults', 'search')
//                 ->press('Search')
//                 ->assertResponseStatus(200)
//                 ->see('Search Yeilded No Results');
//     }

    public function uploadFileToDatabaseAndView($upload) {
            $this->createTestTable();

            $this->actingAs($this->admin)
                 ->visit('upload/1')
                 ->assertResponseStatus(200)
                 ->see('Upload files to '.$this->tblname.' Table')
                 ->type('test', 'upFldNme')
                 ->attach(array($this->path.$upload), 'attachments[]')
                 ->press('Upload')
                 ->assertResponseStatus(200)
                 ->see('Upload files to '.$this->tblname.' Table')
                 ->assertFileExists(storage_path('app/'.$this->tblname.'/test/'.$upload));

            $this->visit('data/1/1/view'.'/test/'.$upload)
                 ->assertResponseStatus(200);
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

    public function testViewInvalidRecord() {
           $this->createTestTable();

           // view specific record with a invalid id
           // should produce error messsage that no results
           $this->actingAs($this->admin)
                ->visit('data/1/2000')
                ->assertResponseStatus(200)
                ->see('Search Yeilded No Results');

    }

    public function testImportWithNoRecords() {
           $path = './storage/app/files/test/';
           $file = 'header_only.dat';

           $this->createTestTable($path, $file);

           $this->actingAs($this->admin)
                ->visit('table')
                ->assertResponseStatus(200)
                ->see('0');

           // visit a table with no records
           $this->visit('data/1')
                ->assertResponseStatus(200)
                ->see('Table does not have any records.');

    }

    public function testNullShow() {
           // test to see if passing null for both table id and record produces a error response
           $response = (new DataViewController)->show(null, null);
           $errors = $response->getSession()->get('errors', new Illuminate\Support\MessageBag)->all();
           $this->assertEquals($errors[0], "Invalid Record ID");
    }

       //   public function testViewDisabledCollection() {
       //     $this->createTestTable();

       //     //create empty file to test view file
       //     $emptyFile = 'empty.csv';
       //     $filePath = './storage/app/'.$this->tblname.'/test';
       //     mkdir($filePath);
       //     touch($filePath.'/'.$emptyFile);

       //     if($this->collection == null){
       //        $this->fail('No Collection present');
       //     }

       //     $data = [ 'id' => $this->collection->id, 'clctnName' => $this->collection->clctnName ];

       //     $this->actingAs($this->admin)
       //          ->post('collection.disable', $data)
       //          ->visit('data/1')
       //          ->assertResponseStatus(200)
       //          ->see('Table is disabled');

           //While using a admin account try to disable a collection
       //     $this->actingAs($this->admin)
       //          ->post('collection.disable', [ 'id' => $this->collection->id, 'clctnName' => $this->collection->clctnName ])
       //          // try to visit the disabled table
       //          ->visit('data/1')
       //          ->assertResponseStatus(200)
       //          ->see('Table is disabled');

//           // try to view a record in a disabled table
//           $this->visit('data/1/1')
//                 ->assertResponseStatus(200)
//                 ->see('Table is disabled');

//            //while table is disabled try to view a file
//            $this->visit('data/1/1/view'.'/test/'.$emptyFile)
//                 ->assertResponseStatus(200)
//                 ->see('Table is disabled');

//            //while table is disabled try to force a Search
//            //test bypasses middleware since we are calling
//            //the controller directly and bypassing
//            //the middleware that checks for access
//            $request = new \Illuminate\Http\Request();
//            $request->setLaravelSession($this->manager->driver());
//            $request->session(['search' => "-------"]);
//            $response = (new DataViewController)->search($request, "1", "1");
//            $errors = $response->errors->all();
//            $this->assertEquals($errors[0], "Search Yeilded No Results");

//            //While using a admin account try to enable a collection

//            $this->actingAs($this->admin)
//                 ->post('collection/enable', [ 'id' => $this->collection->id, 'clctnName' => $this->collection->clctnName ])
//                 ->visit('data/1')
//                 ->assertResponseStatus(200)
//                 ->see($this->tblname);

//            $this->cleanup($this->tblname, $this->file);
//     }

  }
?>
