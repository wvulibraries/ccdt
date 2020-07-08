<?php
    # app/tests/feature/admin/CollectionFeatureTest.php
    use App\Models\User;

    class CollectionFeatureTest extends BrowserKitTestCase
    {
        private $admin;  

        private $colName;
        private $tableName;
        
        // location of test files
        private $filePath = './storage/app';

        public function setUp(): void {
          parent::setUp();

          // find admin and test user accounts
          $this->admin = User::where('name', '=', 'admin')->first();

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
        
        /** @test */
        public function it_can_enable_a_collection_with_tables()
        {
          // Generate Test Collection
          $this->testHelper->createDisabledCollectionWithTable($this->colName, $this->tableName);

          // Go to collection page and enable collection
          $this->actingAs($this->admin)
               ->visit('/collection')
               ->see('Collection(s)')
               ->see('Create, import and manage collections here.')
               ->see($this->colName)
               ->see('Enable');

          // Go to collection page and enable collection
          $this->actingAs($this->admin)
               ->visit('/collection')
               ->click('Enable')
               ->press('Confirm')
               ->see('Disable'); 
                 
          // Check that testtable1 is not shown
          $this->actingAs($this->admin)
               ->visit('/table')
               ->see('Table(s)')
               ->see($this->tableName);  
        }        

        /** @test */
        public function it_can_disable_a_collection_with_table()
        {
          // Generate Test Collection with a table
          $collection = $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

          // Go to collection page and see that collection exists
          $this->actingAs($this->admin)
               ->visit('/collection')
               ->see('Collection Name')
               ->see('Create, import and manage collections here.')
               ->see($this->colName)
               ->see('Add Tables')
               ->see('Edit')
               ->see('Disable');

          // Check that testtable1 is shown
          $this->actingAs($this->admin)
               ->visit('/table')
               ->see($this->tableName);   

          // Go to collection page and disable collection
          $this->actingAs($this->admin)
               ->visit('/collection')
               ->click('Disable')
               ->type($this->colName, 'clctnName')
               ->press('Confirm')
               ->see('Enable');

          // Check that testtable1 is not shown
          $this->actingAs($this->admin)
               ->visit('/table')
               ->assertViewMissing($this->tableName);  
        }    

        /** @test */
        public function it_can_enable_a_collection()
        {
          // Generate Test Collection
          $collection = $this->testHelper->createCollection($this->colName, 0);

          // Go to collection page and enable collection
          $this->actingAs($this->admin)
               ->visit('/collection')
               ->see('Collection Name')
               ->see('Create, import and manage collections here.')
               ->see($this->colName)
               ->see('Enable');

          // Go to collection page and enable collection
          $this->actingAs($this->admin)
               ->visit('/collection')
               ->click('Enable')
               ->press('Confirm')
               ->see('Disable');    
        }        

        /** @test */
        public function it_can_disable_a_collection()
        {
          // Generate Test Collection
          $collection = $this->testHelper->createCollection($this->colName);

          // Go to collection page and see that collection exists
          $this->actingAs($this->admin)
               ->visit('/collection')
               ->see('Collection Name')
               ->see('Create, import and manage collections here.')
               ->see($this->colName)
               ->see('Add Tables')
               ->see('Edit')
               ->see('Disable');

          // Go to collection page and disable collection
          $this->actingAs($this->admin)
               ->visit('/collection')
               ->click('Disable')
               ->type($this->colName, 'clctnName')
               ->press('Confirm')
               ->see('Enable');
        }        

        /** @test */
        public function it_can_create_a_collection()
        {
          // Go to collection and create new collection
          $this->actingAs($this->admin)
               ->visit('/collection')
               ->see('Collection Name')
               ->type($this->colName, 'clctnName')
               ->press('Create')
               ->see('Create, import and manage collections here.')
               ->see($this->colName);
        }

        /** @test */
        public function it_can_show_the_collections_page()
        {
            $this->actingAs($this->admin)
                 ->get('/collection')
                 ->assertResponseStatus(200)
                 ->see('CCDT')
                 ->see('Home')
                 ->see('Admin')
                 ->see('Help')
                 ->see('Collection(s)')
                 ->see('Create, import and manage collections here.')
                 ->see('Create Collection(s)');
        }

    }