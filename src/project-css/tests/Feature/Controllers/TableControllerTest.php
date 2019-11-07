<?php
   # app/tests/controllers/TableControllerTest.php

    use Illuminate\Support\Facades\Storage;
    use App\Http\Controllers\TableController;
    use App\Http\Controllers\DataViewController;

    use App\Models\User;
    use App\Models\Table;

    use App\Libraries\TestHelper;

    class TableControllerTest extends BrowserKitTestCase {

    private $admin;
    private $user;
    private $collection;

    public function setUp(): void {
           parent::setUp();
           Artisan::call('migrate');
           Artisan::call('db:seed');

           // find admin and test user accounts
           $this->admin = User::where('name', '=', 'admin')->first();
           $this->user = User::where('name', '=', 'test')->first();
    }

    protected function tearDown(): void {
           Artisan::call('migrate:reset');
           parent::tearDown();
    }

    public function createTestTable($tblname, $path, $file) {
           (new TestHelper)->createCollection('collection1');

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

           // test tables, files and folders that were created
           (new TestHelper)->cleanupTestTables([$file]);
    }

    public function testCMSSelectFileAndTableCreate() {
            $path = './storage/app/files/test/';
            $file = '1A-random.tab';
            $file2 = '1B-random.tab';

            (new TestHelper)->createCollection('collection1');

            $storageFolder = 'flatfiles';

            $fltFleAbsPth = './storage/app'.'/'.$storageFolder.'/';

            copy($path.$file, $fltFleAbsPth.$file);
            copy($path.$file2, $fltFleAbsPth.$file2);

            $this->actingAs($this->admin)
                 ->call('POST', route('selectcmsdis'), ['colID2' => 1, 'cmsdisFiles2' => [$file, $file2]]);

            $this->assertEquals(DB::table('tables')->count(), 2);

            // test tables, files and folders that were created
            (new TestHelper)->cleanupTestTables([$file, $file2]);
    }

    public function testCMSSelectInvalidFile() {
            $path = './storage/app/files/test/';
            $file = 'images.png';

            (new TestHelper)->createCollection('collection1');

            $storageFolder = 'flatfiles';

            $fltFleAbsPth = './storage/app'.'/'.$storageFolder.'/';

            copy($path.$file, $fltFleAbsPth.$file);

            $this->actingAs($this->admin)
                 ->call('POST', route('selectcmsdis'), ['colID2' => 1, 'cmsdisFiles2' => [$file]]);

            $this->assertEquals(DB::table('tables')->count(), 0);

            // test tables, files and folders that were created
            (new TestHelper)->cleanupTestTables([$file]);
    }

    public function testInvalidFileTypeUpload() {
            $tblname = 'importtest'.mt_rand();
            $path = './storage/app/files/test/';
            $file = 'images.png';

            (new TestHelper)->createCollection('collection1');

            $this->actingAs($this->admin)
                 ->visit('table/create')
                 ->type($tblname, 'imprtTblNme')
                 ->type('1', 'colID')
                 ->attach($path.$file, 'fltFile')
                 ->press('Import')
                 ->assertResponseStatus(200)
                 ->see('The flat file must be a file of type: text/plain.');

            // test tables, files and folders that were created
            (new TestHelper)->cleanupTestTables([$file]);
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

            // test tables, files and folders that were created
            (new TestHelper)->cleanupTestTables([$file]);
    }

    public function testCheckFlatFiles() {
            // test to see if 'test.dat' is available
            // using getFiles
            $filesArray = (new TableController)->getFiles('.');
            $this->assertContains('files/test/test.dat', $filesArray);
    }

    public function testSelect() {
            $tblname = 'importtest'.mt_rand();
            $tblname2 = 'importtest'.mt_rand();
            $path = './storage/app/files/test/';
            $file = 'test.dat';

            $this->createTestTable($tblname, $path, $file);

            $this->actingAs($this->admin)
                 ->visit('table/create#slctPnlBdy')
                 ->type($tblname2, 'slctTblNme')
                 ->see('collection1')
                 ->select($file, 'fltFile2')
                 ->press('Select')
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

            // test tables, files and folders that were created
            (new TestHelper)->cleanupTestTables([$file]);
    }

    public function testSelectAndCreateTableThenDisable() {
            $tblname = 'importtest'.mt_rand();
            $path = './storage/app/files/test/';
            $file = 'test.dat';

            $this->createTestTable($tblname, $path, $file);

            // find table by searching on it's name
            $table = Table::where('tblNme', '=', $tblname)->first();

            // While using a admin account try to disable a table
            $this->actingAs($this->admin)
                 ->post('table/restrict', [ 'id' => $table->id ]);
            $table = Table::where('tblNme', '=', $tblname)->first();
            $this->assertEquals('0', $table->hasAccess);

            // test tables, files and folders that were created
            (new TestHelper)->cleanupTestTables([$file]);
    }

    public function testCMSImportFileUploadAndTableCreate() {
           $path = './storage/app/files/test/';
           $file = '1A-random.tab';
           $file2 = '1B-random.tab';

           (new TestHelper)->createCollection('collection1');

           copy($path.$file, sys_get_temp_dir().'/'.$file);
           copy($path.$file2, sys_get_temp_dir().'/'.$file2);

           $files = [new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file, $file, 'application/octet-stream', filesize($path.$file), null, false),
                     new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file2, $file2, 'application/octet-stream', filesize($path.$file2), null, false)];

           $this->actingAs($this->admin)
                ->call('POST', route('importcmsdis'), ['colID' => 1, 'cmsdisFiles' => $files]);

           $this->assertEquals(DB::table('tables')->count(), 2);

           // test tables, files and folders that were created
           (new TestHelper)->cleanupTestTables([$file, $file2]);
    }

    public function testCMSImportInvalidFileUploadAndTableCreate() {
           $path = './storage/app/files/test/';
           $file = 'images.png';

           (new TestHelper)->createCollection('collection1');

           copy($path.$file, sys_get_temp_dir().'/'.$file);

           $files = [new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file, $file, 'application/octet-stream', filesize($path.$file), null, false)];

           $response = $this->actingAs($this->admin)
                ->call('POST', route('importcmsdis'), ['colID' => 1, 'cmsdisFiles' => $files]);

           $this->assertEquals(DB::table('tables')->count(), 0);

           // test tables, files and folders that were created
           (new TestHelper)->cleanupTestTables([$file]);
    }

    public function testCMSImportExistingFile() {
           $path = './storage/app/files/test/';
           $file = '1A-random.tab';

           (new TestHelper)->createCollection('collection1');

           copy($path.$file, './storage/app/flatfiles/'.$file);

           copy($path.$file, sys_get_temp_dir().'/'.$file);

           $files = [new \Illuminate\Http\UploadedFile(sys_get_temp_dir().'/'.$file, $file, 'application/octet-stream', filesize($path.$file), null, false)];

           $response = $this->actingAs($this->admin)
                ->call('POST', route('importcmsdis'), ['colID' => 1, 'cmsdisFiles' => $files]);

           $this->assertEquals(DB::table('tables')->count(), 0);

           // test tables, files and folders that were created
           (new TestHelper)->cleanupTestTables([$file]);
    }

  }
