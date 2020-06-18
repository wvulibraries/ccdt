<?php
    # app/tests/feature/admin/DataViewFeatureTest.php
    use App\Models\User;

    class DataViewFeatureTest extends BrowserKitTestCase
    {
        private $admin;
        
        // location of test files
        private $filePath = './storage/app';

        public function setUp(): void {
            parent::setUp();

            // find admin and test user accounts
            $this->admin = User::where('name', '=', 'admin')->first();

            // Generate Test Collection with a table
            $this->testHelper->createCollectionWithTable('collection1', 'testtable1');
        }

        protected function tearDown(): void {
            // drop testtable1
            \Schema::drop('testtable1');

            // clear folder that was created with the collection
            rmdir($this->filePath.'/collection1');           

            parent::tearDown();
        }  

        /** @test */
        public function search_table()
        {
           $this->testHelper->insertTestRecord('testtable1', 'John', 'Doe');

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
           $this->testHelper->insertTestRecord('testtable1', 'John', 'Doe');

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
            $this->testHelper->seedTestTable('testtable1', 10);

            $this->actingAs($this->admin)
                 ->get('/data/1/1')
                 ->assertResponseStatus(200);
        }       

        /** @test */
        public function viewing_table_with_records()
        {
            $this->testHelper->seedTestTable('testtable1', 10);

            $this->actingAs($this->admin)
                 ->get('/data/1')
                 ->assertResponseStatus(200)
                 ->see('testtable1');
        }         

        /** @test */
        public function view_table_record()
        {
            $this->testHelper->seedTestTable('testtable1', 10);

            $this->actingAs($this->admin)
                 ->get('/data/1/1')
                 ->assertResponseStatus(200)
                 ->see('1');
        }
        
        /** @test */
        public function view_table_invalid_record()
        {
           $this->testHelper->insertTestRecord('testtable1', 'John', 'Doe');

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