<?php
  # app/tests/controllers/TableControllerTest.php

  use Illuminate\Support\Facades\Storage;
  use App\Http\Controllers\TableController;

  class TableControllerTest extends TestCase {

    private $admin;
    private $user;
    private $collection;

    public function setUp() {
         parent::setUp();
         Artisan::call('migrate');
         Artisan::call('db:seed');

         // find admin and test user accounts
         $this->admin = App\User::where('name', '=', 'admin')->first();
         $this->user = App\User::where('name', '=', 'test')->first();

         // Generate Test Collection
         $this->collection = factory(App\Collection::class)->create([
             'clctnName' => 'collection1',
         ]);
    }

    protected function tearDown() {
      Artisan::call('migrate:reset');
      parent::tearDown();
    }

    public function testNonAdminCannotCreateTable() {
        // try to get to the user(s) page
        $this->actingAs($this->user)
            ->get('table')
            //invalid user gets redirected
            ->assertResponseStatus(302);
    }

    public function testFileUploadAndTableCreate() {
        $tblname = 'importtest'.mt_rand();
        $this->actingAs($this->admin)
             ->visit('table/create')
             ->type($tblname, 'imprtTblNme')
             ->type('1', 'colID')
             ->attach('./storage/app/files/test/zillow.csv', 'fltFile')
             ->press('Import')
             ->assertResponseStatus(200)
             ->see('Edit Schema')
             ->submitForm('Submit', [ 'col-0-data' => 'integer', 'col-0-size' => 'default',
                                     'col-1-data' => 'integer', 'col-1-size' => 'medium',
                                     'col-2-data' => 'integer', 'col-2-size' => 'big',
                                     'col-3-data' => 'string', 'col-3-size' => 'default',
                                     'col-4-data' => 'string', 'col-4-size' => 'medium',
                                     'col-5-data' => 'string', 'col-5-size' => 'big',
                                     'col-6-data' => 'text', 'col-6-size' => 'default' ])
             ->assertResponseStatus(200)
             ->see('Load Data')
             ->press('Load Data')
             ->see('Table(s)')
             ->assertResponseStatus(200);

         // cleanup remove directory for the test table
         Storage::deleteDirectory($tblname);

         // drop table after Testing
         Schema::drop($tblname);
    }

    public function testInvalidFileTypeUpload() {
        $tblname = 'importtest'.mt_rand();
        $this->actingAs($this->admin)
             ->visit('table/create')
             ->type($tblname, 'imprtTblNme')
             ->type('1', 'colID')
             ->attach('./storage/app/files/test/images.png', 'fltFile')
             ->press('Import')
             ->assertResponseStatus(200)
             ->see('The flat file must be a file of type: text/plain.');

        // cleanup remove directory for the test table
        Storage::deleteDirectory($tblname);

        // cleanup remove images.png from upload folder
        // if it exists
        Storage::delete('/flatfiles/images.png');
    }

    public function testFileExistsUpload() {
        $tblname = 'importtest'.mt_rand();
        $this->actingAs($this->admin)
             ->visit('table/create')
             ->type($tblname, 'imprtTblNme')
             ->type('1', 'colID')
             ->attach('./storage/app/files/test/zillow.csv', 'fltFile')
             ->press('Import')
             ->assertResponseStatus(200)
             ->see('File already exists. Please select the file or rename and re-upload.');

        // cleanup remove directory for the test table
        Storage::deleteDirectory($tblname);
    }

    public function testCheckFlatFiles() {
        // test to see if 'zillow.csv' is available
        // using getFiles
        $filesArray = (new TableController)->getFiles('.');
        $this->assertContains('files/test/zillow.csv', $filesArray);
    }

    public function testSchema() {
        if (File::exists(storage_path('/flatfiles/mlb_players.csv'))) {
          // check for a valid file
          $result = (new TableController)->schema('/files/test/mlb_players.csv');
          $this->assertEquals($result[ 0 ], 'Name');
        }

        // passing a filename that doesn't exits should produce false result
        $this->assertFalse((new TableController)->schema('/files/test/unknown.csv'));

        // passing a file that isn't of the correct type should produce false result
        $this->assertFalse((new TableController)->schema('/files/test/images.png'));

        //passing a empty file should produce a false result
        $emptyFile = './storage/app/files/test/empty.csv';
        touch($emptyFile);
        $this->assertFalse((new TableController)->schema('/files/test/empty.csv'));
        unlink($emptyFile);
    }

    public function testSelectAndCreateTableThenDisable() {
        $tblname = 'importtest'.mt_rand();
        $this->actingAs($this->admin)
             ->visit('table/create')
             ->submitForm('Select', [ 'slctTblNme' => $tblname, 'colID' => '1', 'fltFile' => 'zillow.csv' ])
             ->assertResponseStatus(200)
             ->see('Edit Schema')
             ->submitForm('Submit', [ 'col-0-data' => 'integer', 'col-0-size' => 'default',
                                     'col-1-data' => 'string', 'col-1-size' => 'default',
                                     'col-2-data' => 'string', 'col-2-size' => 'medium',
                                     'col-3-data' => 'string', 'col-3-size' => 'big',
                                     'col-4-data' => 'text', 'col-4-size' => 'default',
                                     'col-5-data' => 'text', 'col-5-size' => 'medium',
                                     'col-6-data' => 'text', 'col-6-size' => 'big' ])
             ->assertResponseStatus(200)
             ->see('Load Data')
             ->press('Load Data')
             ->assertResponseStatus(200)
             ->see('Table(s)')
             ->assertResponseStatus(200);

        // find table by searching on it's name
        $table = App\Table::where('tblNme', '=', $tblname)->first();

        // While using a admin account try to disable a table
        $this->actingAs($this->admin)
             ->post('table/restrict', [ 'id' => $table->id ]);
        $table = App\Table::where('tblNme', '=', $tblname)->first();
        $this->assertEquals('0', $table->hasAccess);

        // cleanup remove directory for the test table
        Storage::deleteDirectory($tblname);

        // cleanup remove zillow.csv from upload folder
        Storage::delete('/flatfiles/zillow.csv');

        // drop table after Testing
        Schema::drop($tblname);
    }

    public function testLoad() {
        // calling load should return items one is the list of files
        // in the flatfile directory under Storage
        // we are testing that the array is present and valid
        $response = (new TableController)->load();
        $this->assertInternalType('array', $response->fltFleList);
    }

  }
?>
