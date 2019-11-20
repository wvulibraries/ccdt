<?php
    # app/tests/controllers/HomeControllerTest.php
    use App\Models\User;
    use App\Libraries\TestHelper;

    class CollectionFeatureTest extends BrowserKitTestCase
    {
        private $admin;

        public function setUp(): void {
            parent::setUp();
            Artisan::call('migrate:fresh --seed');

            // find admin and test user accounts
            $this->admin = User::where('name', '=', 'admin')->first();
        }

        protected function tearDown(): void {
            Artisan::call('migrate:rollback');
            parent::tearDown();
        }  
        
        /** @test */
        public function it_can_enable_a_collection_with_tables()
        {
            // Generate Test Collection
            $collection = (new TestHelper)->createDisabledCollectionWithTable('collection1', 'testtable1');

            // Go to collection page and enable collection
            $this->actingAs($this->admin)
                 ->visit('/collection')
                 ->see('Collection Name')
                 ->see('Create, import and manage collections here.')
                 ->see('collection1')
                 ->see('Enable');

            // Go to collection page and enable collection1
            $this->actingAs($this->admin)
                 ->visit('/collection')
                 ->click('Enable')
                 ->press('Confirm')
                 ->see('Disable');  
                 
            // Check that testtable1 is not shown
            $this->actingAs($this->admin)
                 ->visit('/table');
            //      ->see('testtable1');            
        }        

        /** @test */
        public function it_can_disable_a_collection_with_table()
        {
            // Generate Test Collection
            $collection = (new TestHelper)->createCollectionWithTable('collection1', 'testtable1');

            // Go to collection page and see that collection1 exists
            $this->actingAs($this->admin)
                 ->visit('/collection')
                 ->see('Collection Name')
                 ->see('Create, import and manage collections here.')
                 ->see('collection1')
                 ->see('Add Tables')
                 ->see('Edit')
                 ->see('Disable');

            // Go to collection page and disable collection1
            $this->actingAs($this->admin)
                 ->visit('/collection')
                 ->click('Disable')
                 ->type('collection1', 'clctnName')
                 ->press('Confirm')
                 ->see('Enable');

            // Check that testtable1 is not shown
            $this->actingAs($this->admin)
                 ->visit('/table')
                 ->assertViewMissing('testtable1');                
        }    

        /** @test */
        public function it_can_enable_a_collection()
        {
            // Generate Test Collection
            $collection = (new TestHelper)->createDisabledCollection('collection1');

            // Go to collection page and enable collection
            $this->actingAs($this->admin)
                 ->visit('/collection')
                 ->see('Collection Name')
                 ->see('Create, import and manage collections here.')
                 ->see('collection1')
                 ->see('Enable');

            // Go to collection page and enable collection1
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
            $collection = (new TestHelper)->createCollection('collection1');

            // Go to collection page and see that collection1 exists
            $this->actingAs($this->admin)
                 ->visit('/collection')
                 ->see('Collection Name')
                 ->see('Create, import and manage collections here.')
                 ->see('collection1')
                 ->see('Add Tables')
                 ->see('Edit')
                 ->see('Disable');

            // Go to collection page and disable collection1
            $this->actingAs($this->admin)
                 ->visit('/collection')
                 ->click('Disable')
                 ->type('collection1', 'clctnName')
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
                 ->type('collection1', 'clctnName')
                 ->press('Create')
                 ->see('Create, import and manage collections here.')
                 ->see('collection1');

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