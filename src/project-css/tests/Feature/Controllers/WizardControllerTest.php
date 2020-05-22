<?php
   # app/tests/controllers/TableControllerTest.php

    use Illuminate\Support\Facades\Storage;
    use App\Http\Controllers\TableController;
    use App\Http\Controllers\DataViewController;

    use App\Models\User;
    use App\Models\Table;

    class WizardControllerTest extends BrowserKitTestCase {
       private $admin;
       private $user;

       public function setUp(): void {
          parent::setUp();

          // find admin and test user accounts
          $this->admin = User::where('name', '=', 'admin')->first();
          $this->user = User::where('name', '=', 'test')->first();
       }

       public function testImportCollection() {
          // Generate Test Collection
          $this->testHelper->createCollection('collection1');

          $tblname = 'importtest'.mt_rand();

          $this->actingAs($this->admin)
               ->visit('admin/wizard/import/collection/1')
               ->see('Select or Import')
               ->type($tblname, 'imprtTblNme')
               ->type('1', 'colID')
               ->attach('./storage/app/files/test/mlb_players.csv', 'fltFile')
               ->press('Import')
               ->assertResponseStatus(200)
               ->see('mlb_players.csv has been queued for import to '. $tblname .' table. It will be available shortly.');  
               
          // try uploading file 2nd time should return error
          $this->actingAs($this->admin)
               ->visit('admin/wizard/import/collection/1')
               ->see('Select or Import')
               ->type('importtest'.mt_rand(), 'imprtTblNme')
               ->type('1', 'colID')
               ->attach('./storage/app/files/test/mlb_players.csv', 'fltFile')
               ->press('Import')
               ->assertResponseStatus(200)
               ->see('mlb_players.csv File already exists. Please select the file or rename and re-upload.');  

          // test tables, files and folders that were created
          $this->testHelper->cleanupTestTables(['mlb_players.csv']);

          // Delete Test Collection
          $this->testHelper->deleteCollection('collection1');
       }

//     public function testFlatFileUpload() {
//        // Generate Test Collection
//        $this->testHelper->createCollection('collection1');

//        $tblname = 'importtest'.mt_rand();

//        $this->visit('admin/wizard/import/collection/1')
//               ->see('Select or Import')
//               ->type($tblname, 'imprtTblNme')
//               ->type('1', 'colID')
//               ->attach('./storage/app/files/test/mlb_players.csv', 'fltFile')
//               ->press('Import')
//               ->assertResponseStatus(200)
//               ->see('mlb_players.csv has been queued for import to '. $tblname .' table. It will be available shortly.'); 
       
       
       
//     }

//     public function testFlatFileSelect() {
         
//     }

//     public function testCMSUpload() {

//     }

//     public function testCMSSelect() {
//        $path = './storage/app/files/test/';
//        $file = '1A-random.tab';
//        $file2 = '1B-random.tab';

//        $this->testHelper->createCollection('collection1');

//        $storageFolder = 'flatfiles';

//        $fltFleAbsPth = './storage/app'.'/'.$storageFolder.'/';

//        copy($path.$file, $fltFleAbsPth.$file);
//        copy($path.$file2, $fltFleAbsPth.$file2);

//        $this->actingAs($this->admin)
//               ->withoutMiddleware()
//               ->call('POST', route('selectcmsdis'), ['colID2' => 1, 'cmsdisFiles2' => [$file, $file2]]);

//        $this->assertEquals(DB::table('tables')->count(), 2);

//        // test tables, files and folders that were created
//        $this->testHelper->cleanupTestTables([$file, $file2]);

//        // clear folder that was created with the collection
//        rmdir($this->filePath.'/collection1');           
//     }


//     public function testNonAdminCannotRunImportWizard() {
//            // try to get to the wizard page
//            $this->actingAs($this->user)
//                 ->get('wizard.import')
//                 //invalid user gets redirected
//                 ->assertResponseStatus(302);
//     }
  }
