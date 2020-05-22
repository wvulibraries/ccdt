<?php
    # app/tests/feature/admin/TableFeatureTest.php
    use App\Models\User;

    class TableFeatureTest extends BrowserKitTestCase
    {
        private $admin;
        private $user;

        public function setUp(): void {
            parent::setUp();

            // find admin and test user accounts
            $this->admin = User::where('name', '=', 'admin')->first();
            $this->user = User::where('name', '=', 'test')->first();
        }

        public function testCreateTable() {
            //try to import a table without a collection
            $this->actingAs($this->admin)
                ->visit('table/create')
                ->see('Please create active collection here first')
                ->assertResponseStatus(200);

            // Generate Test Collection
            $this->testHelper->createCollection('collection1');

            $tblname = 'importtest'.mt_rand();

            $this->visit('table/create')
                ->see('Add Tables')
                ->click('Add Tables')
                ->see('Select or Import')
                ->type($tblname, 'imprtTblNme')
                ->type('1', 'colID')
                ->attach('./storage/app/files/test/mlb_players.csv', 'fltFile')
                ->press('Import')
                ->assertResponseStatus(200)
                ->see('mlb_players.csv has been queued for import to '. $tblname .' table. It will be available shortly.');  
                
            // delete test tables, files and folders that were created
            $this->testHelper->cleanupTestTables(['mlb_players.csv']);

            // Delete Test Collection
            $this->testHelper->deleteCollection('collection1');            
        }

    }