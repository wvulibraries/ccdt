<?php
    # app/tests/feature/admin/DataViewFeatureTest.php
    use App\Models\User;
    use App\Adapters\SearchIndexAdapter; 

    class DataViewFeatureTest extends BrowserKitTestCase
    {
        private $admin;

        private $colName;
        private $tableName;        
        
        // location of test files
        private $filePath = './storage/app';

        public $searchAdapter;

        public function setUp(): void {
            parent::setUp();

            // find admin and test user accounts
            $this->admin = User::where('name', '=', 'admin')->first();

            $this->searchAdapter = new SearchIndexAdapter;

            // Generate Collection Name
            $this->colName = $this->testHelper->generateCollectionName();

            // Generate Table Name
            $this->tableName = $this->testHelper->createTableName();            

            // Generate Test Collection with a table
            $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);
        }

        protected function tearDown(): void {
            // test tables, files and folders that were created
            $this->testHelper->cleanupTestTables();

            // Delete Test Collections
            $this->testHelper->deleteTestCollections();         

            parent::tearDown();
        }  

        /** @test */
        public function search_table()
        {
           $this->testHelper->insertTestRecord($this->tableName, 'John', 'Doe');

           // process table inserting srchindex for each records
           $this->searchAdapter->process($this->tableName);           

           //search for a name this will go to the fulltext search
           $this->actingAs($this->admin)
                ->visit('data/1')
                ->type('Doe', 'search')
                ->press('Search')
                ->assertResponseStatus(200)
                ->see('John');
        }    

        /** @test */
        public function search_table_no_results()
        {
           $this->testHelper->insertTestRecord($this->tableName, 'John', 'Doe');

           // process table inserting srchindex for each records
           $this->searchAdapter->process($this->tableName);                   

           //search for a name this will go to the fulltext search
           $this->actingAs($this->admin)
                ->visit('data/1')
                ->type('Jack', 'search')
                ->press('Search')
                ->assertResponseStatus(200)
                ->see('Search Yeilded No Results');
        }    

        /** @test */
        public function show_table_record()
        {
            $this->testHelper->seedTestTable($this->tableName, 10);

            $this->actingAs($this->admin)
                 ->get('/data/1/1')
                 ->assertResponseStatus(200);
        }       

        /** @test */
        public function viewing_table_with_records()
        {
            $this->testHelper->seedTestTable($this->tableName, 10);

            $this->actingAs($this->admin)
                 ->get('/data/1')
                 ->assertResponseStatus(200)
                 ->see($this->tableName);
        }         

        /** @test */
        public function view_table_record()
        {
            $this->testHelper->seedTestTable($this->tableName, 10);

            $this->actingAs($this->admin)
                 ->get('/data/1/1')
                 ->assertResponseStatus(200)
                 ->see('1');
        }
        
        /** @test */
        public function view_table_invalid_record()
        {
           $this->testHelper->insertTestRecord($this->tableName, 'John', 'Doe');

           //search for a name this will go to the fulltext search
           $this->actingAs($this->admin)
                ->visit('data/1/2')
                ->assertResponseStatus(200)
                ->see('Search Yeilded No Results');
        }    

        /** @test */
        public function viewing_table_without_records_causes_redirect()
        {
            $this->actingAs($this->admin)
                 ->get('/data/1')
                 ->assertRedirectedToRoute('home');
        }      
        
    }