<?php
  # app/tests/controllers/UploadControllerTest.php

  use App\Http\Controllers\UploadController;
  use Illuminate\Http\UploadedFile;
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Foundation\Testing\WithoutMiddleware;
  use Illuminate\Foundation\Testing\DatabaseMigrations;
  use Illuminate\Foundation\Testing\DatabaseTransactions;

  class UploadControllerTest extends TestCase{

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

    public function testUploadFileToDisabledTable() {
      //try to import a table without a collection
      $this->actingAs($this->admin)
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
           ->assertResponseStatus(200);

       // find table by searching on it's name
       $table = App\Table::where('tblNme', '=', $tblname)->first();

       // While using a admin account try to disable a table
       $this->actingAs($this->admin)->post('table/restrict', ['id' => $table->id]);
       $table = App\Table::where('tblNme', '=', $tblname)->first();
       $this->assertEquals('0', $table->hasAccess);

       $this->visit('upload/1')
             ->assertResponseStatus(200)
             ->see('Table is disabled');

      // cleanup remove mlb_players.csv from upload folder
      Storage::delete('/flatfiles/mlb_players.csv');

      // cleanup remove directory for the test table
      Storage::deleteDirectory($tblname);

      // drop table after Testing
      Schema::drop($tblname);
    }

    public function testUploadFile() {
      //try to import a table without a collection
      $this->actingAs($this->admin)
           ->visit('table/create')
           ->see('Please create active collection here first')
           ->assertResponseStatus(200);

      // Generate Test Collection
      $collection = factory(App\Collection::class)->create([
          'clctnName' => 'collection1',
      ]);

      $tblname = 'importtest' . mt_rand();

      $filestoattch = [];

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
           ->visit('upload/1')
           ->assertResponseStatus(200)
           ->see('Upload files to ' . $tblname . ' Table')
           ->type('test', 'upFldNme')
           ->attach(array('./storage/app/files/test/test_upload.txt'),'attachments[]')
           ->press('Upload')
           ->assertResponseStatus(200)
           ->see('Upload files to ' . $tblname . ' Table')
           ->assertFileExists(storage_path('app/' . $tblname . '/test/test_upload.txt'));

      // The above upload should return 1 message one for a successful upload
      $messages = session()->get('messages');
      $this->assertEquals(count($messages), 1, 'Message Count Should Equal 1');

      // Try to upload the file again causing error
      // since file already exists
      $this->visit('upload/1')
           ->assertResponseStatus(200)
           ->see('Upload files to ' . $tblname . ' Table')
           ->type('test', 'upFldNme')
           ->attach(array('./storage/app/files/test/test_upload.txt'),'attachments[]')
           ->press('Upload')
           ->assertResponseStatus(200)
           ->see('Upload files to ' . $tblname . ' Table')
           ->assertFileExists(storage_path('app/' . $tblname . '/test/test_upload.txt'));

      // The above upload should return 1 message letting user know that the file exists
      $messages = session()->get('messages');
      $this->assertEquals(count($messages), 1, 'Message Count Should Equal 1');

      // cleanup remove test files
      Storage::delete('/flatfiles/mlb_players.csv');
      Storage::delete('/' . $tblname . '/test/test_upload.txt');

      // cleanup remove directory for the test table
      Storage::deleteDirectory($tblname);

      // drop table after Testing
      Schema::drop($tblname);
    }

  }
?>
