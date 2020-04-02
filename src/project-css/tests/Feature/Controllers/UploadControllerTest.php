<?php
    # app/tests/controllers/UploadControllerTest.php

    use Illuminate\Support\Facades\Storage;
    use App\Models\User;
    use App\Models\Collection;
    use App\Models\Table;

    class UploadControllerTest extends BrowserKitTestCase {

    private $admin;
    private $user;

    public function setUp(): void {
        parent::setUp();
        //Artisan::call('migrate:refresh --seed');

        // find admin and test user accounts
        $this->admin = User::where('name', '=', 'admin')->first();
        $this->user = User::where('name', '=', 'test')->first();
    }

    protected function tearDown(): void {
        //Artisan::call('migrate:reset');
        parent::tearDown();
    }

    public function testUploadFile() {
        //try to import a table without a collection
        $this->actingAs($this->admin)
             ->visit('table/create')
             ->see('Please create active collection here first')
             ->assertResponseStatus(200);

        // Generate Test Collection
        $this->testHelper->createCollection('collection1');

        $tblname = 'importtest'.mt_rand();

        $filestoattch = [ ];

        $this->visit('table/create')
             ->type($tblname, 'imprtTblNme')
             ->type('1', 'colID')
             ->attach('./storage/app/files/test/mlb_players.csv', 'fltFile')
             ->press('Import')
             ->assertResponseStatus(200)
             ->see('Edit Schema')
             ->submitForm('Submit', [ 'col-0-data' => 'string', 'col-0-size' => 'default',
                                     'col-1-data' => 'string', 'col-1-size' => 'default',
                                     'col-2-data' => 'string', 'col-2-size' => 'default',
                                     'col-3-data' => 'integer', 'col-3-size' => 'default',
                                     'col-4-data' => 'integer', 'col-4-size' => 'default',
                                     'col-5-data' => 'integer', 'col-5-size' => 'default' ])
             ->assertResponseStatus(200)
             ->see('Load Data')
             ->press('Load Data')
             ->see('Table(s)')
             ->assertResponseStatus(200)
             ->visit('upload/1')
             ->assertResponseStatus(200)
             ->see('Please upload files that will be linked to this collection.')
             ->type('test', 'upFldNme')
             ->attach(array('./storage/app/files/test/test_upload.txt'), 'attachments[]')
             ->press('Upload')
             ->assertResponseStatus(200)
             ->see('Please upload files that will be linked to this collection.')
             ->assertFileExists(storage_path('app/collection1/test/test_upload.txt'));

        // The above upload should return 1 message one for a successful upload
        $messages = session()->get('messages');
        $this->assertEquals(count($messages), 1, 'Message Count Should Equal 1');

        // Try to upload the file again causing error
        // since file already exists
        $this->visit('upload/1')
             ->assertResponseStatus(200)
             ->see('Upload files to collection1 Collection')
             ->type('test', 'upFldNme')
             ->attach(array('./storage/app/files/test/test_upload.txt'), 'attachments[]')
             ->press('Upload')
             ->assertResponseStatus(200)
             ->see('test_upload.txt Already Exists');

        // The above upload should return 1 message letting user know that the file exists
        $messages = session()->get('messages');
        $this->assertEquals(count($messages), 1, 'Message Count Should Equal 1');

        // cleanup remove test files
        $this->testHelper->cleanupTestTables(['mlb_players.csv']);
    } 

  }
