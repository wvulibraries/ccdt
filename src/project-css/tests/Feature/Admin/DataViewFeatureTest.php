<?php
    # app/tests/feature/admin/DataViewFeatureTest.php
    use App\Models\User;
    use App\Libraries\TestHelper;

    class DataViewFeatureTest extends BrowserKitTestCase
    {
        private $admin;
        public $faker;

        public function setUp(): void {
            parent::setUp();
            Artisan::call('migrate:fresh --seed');

            // find admin and test user accounts
            $this->admin = User::where('name', '=', 'admin')->first();
            $this->faker = Faker\Factory::create();
        }

        protected function tearDown(): void {
            Artisan::call('migrate:rollback');
            parent::tearDown();
        }  
        
        public function seedTestTable($tblNme, $items) {
            $insertString = "insert into $tblNme (firstname, lastname) values(?, ?)";

            for ($x = 0; $x <= $items; $x++) {
               //insert record into table for testing
               \DB::insert($insertString,[$this->faker->firstName, $this->faker->lastName]);
            } 
        }    

        /** @test */
        public function show_table_record()
        {
            // Generate Test Collection with a table
            $collection = (new TestHelper)->createCollectionWithTable('collection1', 'testtable1');
            $this->seedTestTable('testtable1', 10);

            $this->actingAs($this->admin)
                 ->get('/data/1/1')
                 ->assertResponseStatus(200);

            // drop testtable1
            \Schema::drop('testtable1');
        }       

        /** @test */
        public function viewing_table_with_records()
        {
            // Generate Test Collection with a table
            $collection = (new TestHelper)->createCollectionWithTable('collection1', 'testtable1');
            $this->seedTestTable('testtable1', 10);

            $this->actingAs($this->admin)
                 ->get('/data/1')
                 ->assertResponseStatus(200)
                 ->see('testtable1');

            // drop testtable1
            \Schema::drop('testtable1');
        }         

        /** @test */
        public function viewing_table_without_records_causes_redirect()
        {
            // Generate Test Collection with a table
            $collection = (new TestHelper)->createCollectionWithTable('collection1', 'testtable1');

            $this->actingAs($this->admin)
                 ->get('/data/1')
                 ->assertRedirectedToRoute('home');

            // drop testtable1
            \Schema::drop('testtable1');
        }      
        
    }