<?php
    # app/tests/controllers/UploadControllerTest.php

    use Illuminate\Support\Facades\Storage;
    use App\Models\User;
    use App\Models\Collection;
    use App\Models\Table;

    class UploadControllerTest extends BrowserKitTestCase {

    private $admin;
    private $user;

    private $colName;     

    public function setUp(): void {
        parent::setUp();

        // find admin and test user accounts
        $this->admin = User::where('name', '=', 'admin')->first();
        $this->user = User::where('name', '=', 'test')->first();

        // Generate Collection Name
        $this->colName = $this->testHelper->generateCollectionName();        
    }

    protected function tearDown(): void {
        // Delete Test Collections
        $this->testHelper->deleteTestCollections();         

        parent::tearDown();
    }     

    public function testUploadFile() {
        // Generate Test Collection
        $this->testHelper->createCollection($this->colName);

        $this->actingAs($this->admin)
             ->visit('upload/1')
             ->assertResponseStatus(200)
             ->see('Please upload files that will be linked to this collection.')
             ->type('test', 'upFldNme')
             ->attach(array('./storage/app/files/test/test_upload.txt'), 'attachments[]')
             ->press('Upload')
             ->assertResponseStatus(200)
             ->see('Please upload files that will be linked to this collection.')
             ->assertFileExists(storage_path('app/'. $this->colName .'/test/test_upload.txt'));

        // The above upload should return 1 message one for a successful upload
        $messages = session()->get('messages');
        $this->assertEquals(count($messages), 1, 'Message Count Should Equal 1');

        // Try to upload the file again causing error
        // since file already exists
        $this->visit('upload/1')
             ->assertResponseStatus(200)
             ->see('Upload files to ' . $this->colName . ' Collection')
             ->type('test', 'upFldNme')
             ->attach(array('./storage/app/files/test/test_upload.txt'), 'attachments[]')
             ->press('Upload')
             ->assertResponseStatus(200)
             ->see('test_upload.txt Already Exists');

        // The above upload should return 1 message letting user know that the file exists
        $messages = session()->get('messages');
        $this->assertEquals(count($messages), 1, 'Message Count Should Equal 1');     
    } 

    public function testUploadFileWithInvalidCollection() {
        $this->actingAs($this->admin)
             ->visit('upload/1')
             ->assertResponseStatus(200)
             ->see('Collection id is invalid');    
    }     

  }
