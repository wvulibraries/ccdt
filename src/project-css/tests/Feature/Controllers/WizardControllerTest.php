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
    private $collection;

    public function setUp(): void {
           parent::setUp();
           //Artisan::call('migrate:refresh --seed');

           // find admin and test user accounts
           $this->admin = User::where('name', '=', 'admin')->first();
           $this->user = User::where('name', '=', 'test')->first();
    }

    protected function tearDown(): void {
           //Artisan::call('migrate:reset');
           parent::tearDown();
    }

//     public function testNonAdminCannotRunImportWizard() {
//            // try to get to the wizard page
//            $this->actingAs($this->user)
//                 ->get('wizard.import')
//                 //invalid user gets redirected
//                 ->assertResponseStatus(302);
//     }
  }
