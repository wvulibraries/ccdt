<?php
    # app/tests/feature/admin/TableFeatureTest.php
    use App\Models\User;

    class TableFeatureTest extends BrowserKitTestCase
    {
        private $admin;
        private $user;

        private $colName;
        private $tableName;          

        public function setUp(): void {
            parent::setUp();

            // find admin and test user accounts
            $this->admin = User::where('name', '=', 'admin')->first();
            $this->user = User::where('name', '=', 'test')->first();

            // Generate Collection Name
            $this->colName = $this->testHelper->generateCollectionName();

            // Generate Table Name
            $this->tableName = $this->testHelper->createTableName();              
        }

        protected function tearDown(): void {
            // test tables, files and folders that were created
            $this->testHelper->cleanupTestTables();

            // Delete Test Collections
            $this->testHelper->deleteTestCollections();         

            parent::tearDown();
        }          

        public function testCreateTable() {
            //try to import a table without a collection
            $this->actingAs($this->admin)
                ->visit('table/create')
                ->see('Please create active collection here first')
                ->assertResponseStatus(200);

            // Generate Test Collection
            $this->testHelper->createCollection($this->colName);

            $this->visit('table/create')
                 ->see('Add Tables')
                 ->click('Add Tables')
                 ->see('Select or Import')
                 ->type($this->tableName, 'imprtTblNme')
                 ->type('1', 'colID')
                 ->attach('./storage/app/files/test/mlb_players.csv', 'fltFile')
                 ->press('Import')
                 ->assertResponseStatus(200)
                 ->see('mlb_players.csv has been queued for import to '. $this->tableName .' table. It will be available shortly.');  
                
            // delete test tables, files and folders that were created
            $this->testHelper->cleanupTestTablesAndFiles(['mlb_players.csv']);

            // Delete Test Collection
            $this->testHelper->deleteCollection($this->colName);            
        }

    }