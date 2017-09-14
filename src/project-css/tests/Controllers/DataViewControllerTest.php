<?php
  # app/tests/controllers/TableControllerTest.php

  use App\Http\Controllers\DataViewController;
  use Illuminate\Http\UploadedFile;
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Foundation\Testing\WithoutMiddleware;
  use Illuminate\Foundation\Testing\DatabaseMigrations;
  use Illuminate\Foundation\Testing\DatabaseTransactions;

  class DataViewControllerTest extends TestCase {

    private $adminEmail;
    private $adminPass;
    private $userName;
    private $userEmail;
    private $userPass;

    public function setUp(){
      parent::setUp();
      Artisan::call('migrate');
      Artisan::call('db:seed');

      //admin credentials
      $this->adminEmail = "admin@admin.com";
      $this->adminPass = "testing";

      //user credentials
      $this->userName = "testuser";
      $this->userEmail = "testuser@google.com";
      $this->userPass = "testing";
    }

    protected function tearDown() {
      Artisan::call('migrate:reset');
      parent::tearDown();
    }

    public function testIndex(){
      // find admin user
      $admin = App\User::where('isAdmin', '=', '1')->first();

      //try to import a table without a collection
      $this->actingAs($admin)
           ->visit('table/create')
           ->see('Please create active collection here first')
           ->assertResponseStatus(200);

      // Generate Test Collection
      $collection = factory(App\Collection::class)->create([
          'clctnName' => 'collection1',
      ]);

      $tblname = 'importtest' . mt_rand();

      $this->visit('table/create')
           ->type($tblname,'imprtTblNme')
           ->type('1','colID')
           ->attach('./storage/app/files/test/mlb_players.csv','fltFile')
           ->press('Import')
           ->assertResponseStatus(200)
           ->see('Edit Schema')
           ->submitForm('Submit', ['col-0-data' => 'string', 'col-0-size' => 'default',
                                   'col-1-data' => 'string', 'col-1-size' => 'default',
                                   'col-2-data' => 'string', 'col-2-size' => 'default',
                                   'col-3-data' => 'integer', 'col-3-size' => 'default',
                                   'col-4-data' => 'integer', 'col-4-size' => 'default',
                                   'col-5-data' => 'integer', 'col-5-size' => 'default'])
           ->assertResponseStatus(200)
           ->see('Load Data')
           ->press('Load Data')
           ->see('Table(s)')
           ->assertResponseStatus(200)
           ->visit('data/1')
           ->see($tblname . ' Records')
           ->visit('data/1/1')
           ->assertResponseStatus(200)
           ->see('Adam Donachie');

      // test isValidTable
      $this->assertTrue((new DataViewController)->isValidTable('1'));

      //create empty file to test view file
      $emptyFile = 'empty.csv';
      $filePath = './storage/app/' . $tblname . '/test';
      mkdir($filePath);
      touch($filePath . '/' . $emptyFile);

      // While using a admin account try to disable a collection
      $this->post('collection/disable', ['id' => $collection->id, 'clctnName' => $collection->clctnName])
           // try to visit the disabled table
           ->visit('data/1')
           ->assertResponseStatus(200)
           ->see('Table is disabled')
           // try to view a record in a disabled table
           ->visit('data/1/1')
           ->assertResponseStatus(200)
           ->see('Table is disabled');

      // while table is disabled try to view a file
      $this->visit('data/1/view' . '/test/' . $emptyFile)
            ->assertResponseStatus(200);

      // While using a admin account try to enable a collection
      $this->post('collection/enable', ['id' => $collection->id, 'clctnName' => $collection->clctnName])
           ->visit('data/1')
           ->assertResponseStatus(200);

      // search for a name this will go to the fulltext search
      $this->visit('data/1/1')
           ->type('Adam Donachie','search')
           ->press('Search')
           ->assertResponseStatus(200)
           ->see('Adam Donachie');

      // search for a number that will go to the basic "like" search
      // $this->visit('data/1/1
      //      ->type('75','search')
      //      ->press('Search')
      //      ->assertResponseStatus(200)
      //      ->see('75');

      // view specific record
      $this->visit('data/1/19')
           ->assertResponseStatus(200)
           ->see('Hayden Penn');

      // view specific record with a invalid id
      // should produce invalid id message
      $this->visit('data/1/2000')
           ->assertResponseStatus(200)
           ->see('Search Yeilded No Results');

      $this->visit('data/1/view' . '/test/' . $emptyFile)
           ->assertResponseStatus(200);

      // logout user
      Auth::logout();

      // try to view a record without a authenticated user
      // they should be redirected to the Login page
      $this->visit('data/1/view' . '/test/' . $emptyFile)
           ->see('Login');

      unlink($filePath . '/' . $emptyFile);

      // cleanup remove directory for the test table
      Storage::deleteDirectory($tblname);

      // cleanup remove mlb_players.csv from upload folder
      Storage::delete('/flatfiles/mlb_players.csv');
    }

    public function testImportWithNoRecords(){
      // find admin user
      $admin = App\User::where('isAdmin', '=', '1')->first();

      // Generate Test Collection
      $collection = factory(App\Collection::class)->create([
          'clctnName' => 'collection1',
      ]);

      $tblname = 'importtest' . mt_rand();
      $this->actingAs($admin)
           ->visit('table/create')
           ->type($tblname,'imprtTblNme')
           ->type('1','colID')
           ->attach('./storage/app/files/test/header_only.csv','fltFile')
           ->press('Import')
           ->assertResponseStatus(200)
           ->see('Edit Schema')
           ->submitForm('Submit', ['col-0-data' => 'string', 'col-0-size' => 'default',
                                   'col-1-data' => 'string', 'col-1-size' => 'default',
                                   'col-2-data' => 'string', 'col-2-size' => 'default',
                                   'col-3-data' => 'integer', 'col-3-size' => 'default',
                                   'col-4-data' => 'integer', 'col-4-size' => 'default',
                                   'col-5-data' => 'integer', 'col-5-size' => 'default'])
           ->assertResponseStatus(200)
           ->see('Load Data')
           ->press('Load Data')
           ->assertResponseStatus(200)
           ->see('0 Records');

       // visit a table with no records
       $this->visit('data/1')
            ->assertResponseStatus(200)
            ->see('Table does not have any records.');

       // cleanup remove directory for the test table
       Storage::deleteDirectory($tblname);

       // cleanup remove zillow.csv from upload folder
       Storage::delete('/flatfiles/header_only.csv');
    }

    public function testInvalidTableId(){
        // test to see if table id 99 is available
        // test should fail
        $this->assertFalse((new DataViewController)->isValidTable('99'));
    }

  }
?>
