<?php
use App\Libraries\CSVHelper;
use App\Libraries\CMSHelper;
use App\Libraries\TableHelper;

class TableHelperTest extends TestCase
{
    // use the factory to create a Faker\Generator instance
    public $faker;

    public function setUp() {
       parent::setUp();
       Artisan::call('migrate');
       Artisan::call('db:seed');

       // create test collection
       \DB::insert('insert into collections (clctnName, isEnabled, hasAccess) values(?, ?, ?)',['collection1', true, true]);

       $this->faker = Faker\Factory::create();
    }

    protected function tearDown() {
       Artisan::call('migrate:reset');
       parent::tearDown();
    }

    public function testCreateTableWithHeader() {
      $storageFolder = 'files/test';
      // set location of file
      $fileName = 'zillow.csv';
      // set table name
      $tableName = 'zillow';

      //pass true if contains header row, file location, number of rows to check
      $fieldTypes = (new CSVHelper)->determineTypes(true, $storageFolder.'/'.$fileName, 10);

      // get header from csv file
      $schema = (new CSVHelper)->schema($storageFolder.'/'.$fileName);

      $this->assertEquals(count($schema), count($fieldTypes));

      (new TableHelper)->createTable($storageFolder, $fileName, $tableName, $schema, $fieldTypes, 1);

      $this->assertTrue(Schema::hasTable($tableName));

      // drop test table
      Schema::dropIfExists($tableName);
    }

    public function testCreateTableWithoutHeader() {
      $storageFolder = 'files/test';
      // set location of file
      $fileName = 'zillow-no-header.csv';
      // set table name
      $tableName = 'zillow';

      // get 1st row from csv file
      $schema = (new CSVHelper)->schema($storageFolder.'/'.$fileName);
      //var_dump($schema);

      // detect fields we pass false if we do not have a header row,
      // file location, number of rows to check
      $fieldTypes = (new CSVHelper)->determineTypes(false, $storageFolder.'/'.$fileName, 100);
      //var_dump($fieldTypes);

      // generate $header since one is not provided
      $header = (new CSVHelper)->generateHeader(count($schema));
      //var_dump($header);

      (new TableHelper)->createTable($storageFolder, $fileName, $tableName, $header, $fieldTypes, 1);

      $this->assertTrue(Schema::hasTable($tableName));

      // drop test table
      Schema::dropIfExists($tableName);
    }

    public function testCreateCMSTableWithoutHeader() {
      $storageFolder = 'files/test';
      // set location of file
      $fileName = '1A-random.tab';
      // set table name
      $tableName = 'test1A';

      // get 1st row from csv file
      $schema = (new CSVHelper)->schema($storageFolder.'/'.$fileName);

      // detect fields we pass false if we do not have a header row,
      // file location, number of rows to check
      $fieldTypes = (new CSVHelper)->determineTypes(false, $storageFolder.'/'.$fileName, 100);

      // get header from database
      $header = (new CMSHelper)->getCMSFields($schema[0], count($fieldTypes));

      // if correct header isn't found we generate one
      if (count($fieldTypes) != count($header)) {
        // generate header since we couldn't find a match
        $header = (new CSVHelper)->generateHeader(count($fieldTypes));
      }

      // verify that we are dectecting and additaional field
      $this->assertEquals(count($fieldTypes), count($header));

      (new TableHelper)->createTable($storageFolder, $fileName, $tableName, $header, $fieldTypes, 1);

      $this->assertTrue(Schema::hasTable($tableName));

      // drop test table
      Schema::dropIfExists($tableName);
    }

    function generateRandomID($length = 40) {
        $characters = '0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
