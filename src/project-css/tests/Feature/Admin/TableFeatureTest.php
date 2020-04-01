<?php
    # app/tests/feature/admin/DataViewFeatureTest.php
    use App\Models\User;

    class TableFeatureTest extends BrowserKitTestCase
    {
        private $admin;

        public function setUp(): void {
            parent::setUp();
            //Artisan::call('migrate:fresh --seed');

            // find admin and test user accounts
            $this->admin = User::where('name', '=', 'admin')->first();
        }

        protected function tearDown(): void {
            //Artisan::call('migrate:rollback');
            parent::tearDown();
        }  

        
    }