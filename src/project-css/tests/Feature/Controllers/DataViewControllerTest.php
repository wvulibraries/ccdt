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
    private $tblname;
    private $collection;

    private $path;
    private $file;
    
    // location of test files
    private $filePath = './storage/app';

    /**
    * @var \Illuminate\Session\SessionManager
    */
    protected $manager;

    public function setUp(): void {
       parent::setUp();
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

    protected function tearDown(): void {
       // test tables, files and folders that were created
       $this->testHelper->cleanupTestTables();

       // Delete Test Collections
       $this->testHelper->deleteTestCollections();         

       parent::tearDown();
   }   

   public function createTestTable() {
       $this->path = './storage/app/files/test/';
       $this->file = 'test.dat';

        // Create Test Collection        
        $this->collection = $this->testHelper->createCollection(time(), 1, false);

        // Create Test Table
        $this->tblname = $this->testHelper->createTestTable($this->collection, $this->file);       

       // tests running too fast let job queue import
       sleep(5);  
    }

    public function testInvalidId() {
       $this->createTestTable();

       // view specific record with a invalid id
       // should produce error messsage that no results
       $this->actingAs($this->admin)
            ->visit('table')
            ->see($this->tblname)
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

    public function uploadFileToDatabaseAndView($upload) {
       $this->createTestTable();

       $this->actingAs($this->admin)
              ->visit('upload/1')
              ->assertResponseStatus(200)
              ->see('Upload files to ' . $this->collection->clctnName . ' Collection')
              ->type('test', 'upFldNme')
              ->attach(array($this->path.$upload), 'attachments[]')
              ->press('Upload')
              ->assertResponseStatus(200)
              ->see('Upload files to ' . $this->collection->clctnName . ' Collection')
              ->assertFileExists(storage_path('app/'.$this->collection->clctnName.'/test/'.$upload));

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

  }
