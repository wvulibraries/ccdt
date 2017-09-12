<?php
  # app/tests/controllers/TableControllerTest.php

  use Illuminate\Http\UploadedFile;
  use Illuminate\Support\Facades\Storage;
  use Illuminate\Foundation\Testing\WithoutMiddleware;
  use Illuminate\Foundation\Testing\DatabaseMigrations;
  use Illuminate\Foundation\Testing\DatabaseTransactions;

  use App\Http\Controllers\TableController;

  class TableControllerTest extends TestCase {

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

    public function testNonAdminCannotCreateTable(){
        // find non-admin user
        $user = App\User::where('isAdmin', '=', '0')->first();

        // try to get to the user(s) page
        $this->actingAs($user)
            ->get('table')
            //invalid user gets redirected
            ->assertResponseStatus(302);
    }

    // public function testAdminCanCreateTable(){
    //     // find admin user
    //     $admin = App\User::where('isAdmin', '=', '1')->first();
    //
    //     // try to get to the user(s) page
    //     $this->actingAs($admin)
    //         ->get('table')
    //         //valid admin user should get response 200
    //         ->assertResponseStatus(200);
    // }

    // public function testAdminCreateTable(){
    //     // find admin user
    //     $admin = App\User::where('isAdmin', '=', '1')->first();
    //
    //     // try to get to the table/create page
    //     $this->actingAs($admin)
    //          ->get('table/create')
    //          ->see('Please create active collection here first')
    //          //valid admin user should get response 200
    //          ->assertResponseStatus(200);
    //
    //     // Generate Test Collection
    //     $collection = factory(App\Collection::class)->create([
    //         'clctnName' => 'collection1',
    //     ]);
    //
    //     $this->actingAs($admin)
    //          ->get('table/create')
    //          ->see('Create Table(s) Wizard')
    //          ->assertResponseStatus(200)
    //          ->see('Table Name');
    // }

    public function testFileUploadAndTableCreate(){
        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();

        // Generate Test Collection
        $collection = factory(App\Collection::class)->create([
            'clctnName' => 'collection1',
        ]);

        $tblname = 'importtest' . mt_rand();
        $response = $this->actingAs($admin)
                         ->visit('table/create')
                         ->type($tblname,'imprtTblNme')
                         ->type('1','colID')
                         ->attach('./storage/app/files/test/zillow.csv','fltFile')
                         ->press('Import')
                         ->assertResponseStatus(200)
                         ->see('Edit Schema')
                         ->submitForm('Submit', ['col-0-data' => 'integer', 'col-0-size' => 'default',
                                                 'col-1-data' => 'integer', 'col-1-size' => 'medium',
                                                 'col-2-data' => 'integer', 'col-2-size' => 'big',
                                                 'col-3-data' => 'string', 'col-3-size' => 'default',
                                                 'col-4-data' => 'string', 'col-4-size' => 'medium',
                                                 'col-5-data' => 'string', 'col-5-size' => 'big',
                                                 'col-6-data' => 'text', 'col-6-size' => 'default'])
                         ->assertResponseStatus(200)
                         ->see('Load Data')
                         ->press('Load Data')
                         ->see('Table(s)')
                         ->assertResponseStatus(200);

         // cleanup remove directory for the test table
         Storage::deleteDirectory($tblname);
    }

    public function testInvalidFileTypeUpload(){
        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();

        // Generate Test Collection
        $collection = factory(App\Collection::class)->create([
            'clctnName' => 'collection1',
        ]);

        $tblname = 'importtest' . mt_rand();
        $response = $this->actingAs($admin)
                         ->visit('table/create')
                         ->type($tblname,'imprtTblNme')
                         ->type('1','colID')
                         ->attach('./storage/app/files/test/images.png','fltFile')
                         ->press('Import')
                         ->assertResponseStatus(200)
                         ->see('The flat file must be a file of type: text/plain.');

        // cleanup remove directory for the test table
        Storage::deleteDirectory($tblname);

        // cleanup remove images.png from upload folder
        // if it exists
        Storage::delete('/flatfiles/images.png');
    }

    public function testFileExistsUpload(){
        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();

        // Generate Test Collection
        $collection = factory(App\Collection::class)->create([
            'clctnName' => 'collection1',
        ]);

        $tblname = 'importtest' . mt_rand();
        $response = $this->actingAs($admin)
                         ->visit('table/create')
                         ->type($tblname,'imprtTblNme')
                         ->type('1','colID')
                         ->attach('./storage/app/files/test/zillow.csv','fltFile')
                         ->press('Import')
                         ->assertResponseStatus(200)
                         ->see('File already exists. Please select the file or rename and re-upload.');

        // cleanup remove directory for the test table
        Storage::deleteDirectory($tblname);
    }

    public function testCheckFlatFiles(){
        // test to see if 'zillow.csv' is available
        // using getFiles
        $filesArray = (new TableController)->getFiles('.');
        $this->assertContains( 'files/test/zillow.csv', $filesArray );
    }

    public function testSchema(){
        if (File::exists(storage_path('/flatfiles/mlb_players.csv'))) {
          // check for a valid file
          $result = (new TableController)->schema('/files/test/mlb_players.csv');
          $this->assertEquals($result[0], 'Name');
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

    public function testSelectAndCreateTableThenDisable(){        // find admin user
        $admin = App\User::where('isAdmin', '=', '1')->first();

        // Generate Test Collection
        $collection = factory(App\Collection::class)->create([
            'clctnName' => 'collection1',
        ]);

        $tblname = 'importtest' . mt_rand();
        $response = $this->actingAs($admin)
             ->visit('table/create')
             ->submitForm('Select', ['slctTblNme' => $tblname, 'colID' => '1', 'fltFile' => 'zillow.csv'])
             ->assertResponseStatus(200)
             ->see('Edit Schema')
             ->submitForm('Submit', ['col-0-data' => 'integer', 'col-0-size' => 'default',
                                     'col-1-data' => 'string', 'col-1-size' => 'default',
                                     'col-2-data' => 'string', 'col-2-size' => 'medium',
                                     'col-3-data' => 'string', 'col-3-size' => 'big',
                                     'col-4-data' => 'text', 'col-4-size' => 'default',
                                     'col-5-data' => 'text', 'col-5-size' => 'medium',
                                     'col-6-data' => 'text', 'col-6-size' => 'big'])
             ->assertResponseStatus(200)
             ->see('Load Data')
             ->press('Load Data')
             ->assertResponseStatus(200)
             ->see('Table(s)')
             ->assertResponseStatus(200);

        // find table by searching on it's name
        $table = App\Table::where('tblNme', '=', $tblname)->first();

        // While using a admin account try to disable a table
        $response = $this->actingAs($admin)->post('table/restrict', ['id' => $table->id]);
        $table = App\Table::where('tblNme', '=', $tblname)->first();
        $this->assertEquals('0', $table->hasAccess);

        // cleanup remove directory for the test table
        Storage::deleteDirectory($tblname);

        // cleanup remove zillow.csv from upload folder
        Storage::delete('/flatfiles/zillow.csv');
    }

  }
?>
