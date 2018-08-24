<?php
use \Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Models\Table;
use App\Libraries\CSVHelper;
use App\Libraries\CMSHelper;
use App\Libraries\FileViewHelper;
use App\Libraries\TableHelper;
use App\Libraries\TestHelper;

class TableHelperTest extends TestCase
{
    public function setUp() {
       parent::setUp();
       Artisan::call('migrate');
       Artisan::call('db:seed');
    }

    protected function tearDown() {
       Artisan::call('migrate:reset');
       parent::tearDown();
    }

    public function testCreateTableWithHeader() {
      // set storage location
      $storageFolder = 'files/test';

      // set location of file
      $fileName = 'zillow.csv';

      $tableName = (new TestHelper)->createTable($storageFolder, $fileName, true);

      $this->assertTrue(Schema::hasTable($tableName));

      // get newly created table
      $table = Table::where('tblNme', $tableName)->first();

      // assert field count is equal to 7
      $this->assertEquals($table->getOrgCount(), 7);

      // drop test table
      Schema::dropIfExists($tableName);

      // clear folder that was created with the table
      rmdir('./storage/app'.'/'.$tableName);
    }

    public function testCreateCMSTableWithoutHeader() {
      // set storage location
      $storageFolder = 'files/test';

      // set location of file
      $fileName = '1A-random.tab';

      $tableName = (new TestHelper)->createTable($storageFolder, $fileName, false);

      $this->assertTrue(Schema::hasTable($tableName));

      // get newly created table
      $table = Table::where('tblNme', $tableName)->first();

      // assert field count is equal to 7
      $this->assertEquals($table->getOrgCount(), 13);

      // drop test table
      Schema::dropIfExists($tableName);

      // clear folder that was created with the table
      rmdir('./storage/app'.'/'.$tableName);
    }

    // public function testcreateTablesFromFiles() {
    //   // set storage location
    //   $storageFolder = 'files/test';
    //
    //   // set location of file
    //   $fileName = '1A-random.tab';
    //
    //   $fltFleAbsPth = './storage/app'.'/'.$storageFolder.'/'.$fileName;
    //
    //   $path = sys_get_temp_dir().'/'.$fileName;
    //
    //   copy($fltFleAbsPth, $path);
    //
    //   $file = new \Illuminate\Http\UploadedFile($fltFleAbsPth, $fileName, 'text/csv', filesize($fltFleAbsPth), UPLOAD_ERR_OK, true);
    //   // $response = $this->call('POST', '/table/create', ['cmsdisFiles' => $file], ['Accept' => 'application/json']);
    //   //
    //   // $this->assertFileExists('./storage/app'.'/flatfiles/'.$fileName);
    //
    //   $files = [
    //     'files' => [$file]
    //   ];
    //
    //   $response = $this->actingAs(User::where('name', '=', 'admin')->first())
    //                           ->call('POST', route('table.create/importcmsdis', $project->id), $this->defaultProjectData(), [], $files);
    //
    //
    //   // $this->assertEquals(0, DB::table('tables')->count());
    //   //
    //   // $tableHelper = (new TableHelper);
    //   //
    //   // // create test collection
    //   // $collection = (new TestHelper)->createCollection(time());
    //   //
    //   // $response = $tableHelper->createTablesFromFiles('flatfiles', $collection->id, $files);
    //   //
    //   // $this->assertEquals(DB::table('tables')->count(), 1);
    // }

}
