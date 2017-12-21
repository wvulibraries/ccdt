<?php
  # app/tests/controllers/TableControllerTest.php

  use Illuminate\Support\Facades\Storage;
  use App\Http\Controllers\TableController;
  use App\Http\Controllers\DataViewController;

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
                                         'col-16-data' => 'integer', 'col-16-size' => 'medium',
                                         'col-17-data' => 'string', 'col-17-size' => 'default',
                                         'col-18-data' => 'string', 'col-18-size' => 'default',
                                         'col-19-data' => 'integer', 'col-19-size' => 'big',
                                         'col-20-data' => 'string', 'col-20-size' => 'big',
                                         'col-21-data' => 'text', 'col-21-size' => 'default',
                                         'col-22-data' => 'text', 'col-22-size' => 'medium',
                                         'col-23-data' => 'string', 'col-23-size' => 'default',
                                         'col-24-data' => 'string', 'col-24-size' => 'default',
                                         'col-25-data' => 'string', 'col-25-size' => 'default',
                                         'col-26-data' => 'string', 'col-26-size' => 'default',
                                         'col-27-data' => 'string', 'col-27-size' => 'medium',
                                         'col-28-data' => 'string', 'col-28-size' => 'big',
                                         'col-29-data' => 'text', 'col-29-size' => 'big',
                                         'col-30-data' => 'text', 'col-30-size' => 'big',
                                         'col-31-data' => 'string', 'col-31-size' => 'default' ])
                 ->assertResponseStatus(200)
                 ->see('Load Data')
                 ->press('Load Data')
                 ->see('Table(s)')
                 ->assertResponseStatus(200);

                 // test isValidTable
                 $this->assertTrue((new DataViewController)->isValidTable('1'));
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
        $path = './storage/app/files/test/';
        $file = 'test.dat';

        $this->createTestTable($tblname, $path, $file);

        // cleanup remove $file from upload folder
        Storage::delete('/flatfiles/' . $file);

        // cleanup remove directory for the test table
        Storage::deleteDirectory($tblname);

        // drop table after Testing
        Schema::drop($tblname);
    }

    public function testInvalidFileTypeUpload() {
        $tblname = 'importtest'.mt_rand();

        $this->createCollection('collection1');

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
        $tblname2 = 'importtest'.mt_rand();
        $path = './storage/app/files/test/';
        $file = 'test.dat';

        $this->createTestTable($tblname, $path, $file);

        $this->actingAs($this->admin)
             ->visit('table/create')
             ->type($tblname2, 'imprtTblNme')
             ->type('1', 'colID')
             ->attach($path.$file, 'fltFile')
             ->press('Import')
             ->assertResponseStatus(200)
             ->see('File already exists. Please select the file or rename and re-upload.');

        // cleanup remove directory for the test table
        Storage::deleteDirectory($tblname);

        // drop table after Testing
        Schema::drop($tblname);
    }

    public function testCheckFlatFiles() {
        // test to see if 'test.dat' is available
        // using getFiles
        $filesArray = (new TableController)->getFiles('.');
        $this->assertContains('files/test/test.dat', $filesArray);

        // cleanup remove $file from upload folder
        Storage::delete('/flatfiles/test.dat');
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
        $path = './storage/app/files/test/';
        $file = 'test.dat';

        $this->createTestTable($tblname, $path, $file);

        // find table by searching on it's name
        $table = App\Table::where('tblNme', '=', $tblname)->first();

        // While using a admin account try to disable a table
        $this->actingAs($this->admin)
             ->post('table/restrict', [ 'id' => $table->id ]);
        $table = App\Table::where('tblNme', '=', $tblname)->first();
        $this->assertEquals('0', $table->hasAccess);

        // cleanup remove $file from upload folder
        Storage::delete('/flatfiles/' . $file);

        // cleanup remove directory for the test table
        Storage::deleteDirectory($tblname);

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
