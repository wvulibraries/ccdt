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

    // public function testCreate1ACMSfiles() {
    //     // location of test files
    //     $filePath = './storage/app';
    //     $testFilesFolder = 'files/test';
    //     $recordType = '1A';
    //
    //     $thsFltFile = $recordType . '-random.tab';
    //     $thisFltFileAbs = $filePath.'/'.$testFilesFolder.'/'.$thsFltFile;
    //     touch($thisFltFileAbs);
    //
    //     // get header from database
    //     $header = (new CMSHelper)->getCMSFields($recordType, 13);
    //
    //     $handle = fopen($thisFltFileAbs, "a");
    //
    //     // write header
    //     // fputcsv($handle, $header, "\t");
    //
    //     $rowCount = rand(5, 100);
    //
    //     for ($pos = 0; $pos <= $rowCount; $pos++) {
    //       $randomArr = [];
    //       array_push($randomArr, $recordType); // record type
    //       array_push($randomArr, $this->generateRandomID()); // Constituent ID
    //       array_push($randomArr, $this->faker->randomElement($array = array('AG', 'BU', 'CS', 'FM', 'IN', 'MC', 'OR', 'OT'))); // Individual Type
    //       array_push($randomArr, $this->faker->title); // Prefix
    //       array_push($randomArr, $this->faker->firstName); // First Name
    //       array_push($randomArr, $this->faker->firstName); // Middle Name
    //       array_push($randomArr, $this->faker->lastName); // Last Name
    //       array_push($randomArr, ' '); // Suffix
    //       array_push($randomArr, ' '); // Appellation
    //       array_push($randomArr, $this->faker->randomElement($array = array ('Friend',' '))); // Salutation
    //       array_push($randomArr, $this->faker->date($format = 'Ymd', $max = '-30 years')); // Date of Birth
    //       array_push($randomArr, $this->faker->randomElement($array = array ('Y','N'))); // No Mail Flag
    //       array_push($randomArr, $this->faker->randomElement($array = array ('Y','N'))); // Deceased Flag
    //
    //       fputcsv($handle, $randomArr, "\t");
    //     }
    //     fclose($handle);
    //
    //     $fieldTypes = (new CSVHelper)->determineTypes(false, $testFilesFolder.'/'.$thsFltFile, 100);
    //     (new TableHelper)->createTable($testFilesFolder, $thsFltFile, $recordType, $header, $fieldTypes, 1);
    //
    //     $this->assertTrue(Schema::hasTable($recordType));
    //
    //     // drop test table
    //     Schema::dropIfExists($recordType);
    //
    //     //unlink($thisFltFileAbs);
    //
    //     // clear folder that was created with the table
    //     rmdir($filePath.'/'.$recordType);
    // }

    // public function testCreate1BCMSfiles() {
    //     // location of test files
    //     $filePath = './storage/app';
    //     $testFilesFolder = 'files/test';
    //     $recordType = "1B";
    //
    //     $thsFltFile = $recordType . '-random.tab';
    //     $thisFltFileAbs = $filePath.'/'.$testFilesFolder.'/'.$thsFltFile;
    //     touch($thisFltFileAbs);
    //
    //     // get header from database
    //     $header = (new CMSHelper)->getCMSFields($recordType, 22);
    //
    //     $handle = fopen($thisFltFileAbs, "a");
    //
    //     // write header
    //     // fputcsv($handle, $header, "\t");
    //
    //     $rowCount = rand(5, 100);
    //
    //     for ($pos = 0; $pos <= $rowCount; $pos++) {
    //       $randomArr = [];
    //       array_push($randomArr, $recordType); // record type
    //       array_push($randomArr, $this->generateRandomID(40)); // Constituent ID
    //       array_push($randomArr, $this->generateRandomID(10)); // Address ID
    //       array_push($randomArr, $this->faker->randomElement($array = array ('BU','HO', 'IN', 'OT'))); // Address Type
    //       array_push($randomArr, $this->faker->randomElement($array = array ('S','P'))); // Primary Flag
    //       array_push($randomArr, ''); // Default Address Flag
    //       array_push($randomArr, ''); // Title
    //       array_push($randomArr, $this->faker->company); // Organization Name
    //       array_push($randomArr, $this->faker->streetAddress); // Address line 1
    //       array_push($randomArr, ''); // Address line 2
    //       array_push($randomArr, ''); // Address line 3
    //       array_push($randomArr, ''); // Address line 4
    //       array_push($randomArr, $this->faker->city); // City
    //       array_push($randomArr, $this->faker->stateAbbr); // State
    //       array_push($randomArr, $this->faker->postcode); // Zip Code
    //       array_push($randomArr, $this->faker->word); // County
    //       array_push($randomArr, $this->faker->country); // Country
    //       array_push($randomArr, $this->faker->word); // District
    //       array_push($randomArr, $this->faker->word); // Precinct
    //       array_push($randomArr, $this->faker->randomElement($array = array ('X',''))); // No Mail Flag
    //       array_push($randomArr, ''); // Agency Code
    //       fputcsv($handle, $randomArr, "\t");
    //     }
    //     fclose($handle);
    //
    //     $fieldTypes = (new CSVHelper)->determineTypes(false, $testFilesFolder.'/'.$thsFltFile, 100);
    //     (new TableHelper)->createTable($testFilesFolder, $thsFltFile, $recordType.'testing', $header, $fieldTypes, 1);
    //
    //     $this->assertTrue(Schema::hasTable($recordType.'testing'));
    //
    //     // drop test table
    //     Schema::dropIfExists($recordType);
    //
    //     //unlink($thisFltFileAbs);
    //
    //     // clear folder that was created with the table
    //     rmdir($filePath.'/'.$recordType);
    // }

    // public function testCreate3DCMSfiles() {
    //     // location of test files
    //     $filePath = './storage/app';
    //     $testFilesFolder = 'files/test';
    //     $recordType = "3D";
    //
    //     $thsFltFile = $recordType . '-random.tab';
    //     $thisFltFileAbs = $filePath.'/'.$testFilesFolder.'/'.$thsFltFile;
    //     touch($thisFltFileAbs);
    //
    //     // get header from database
    //     $header = (new CMSHelper)->getCMSFields($recordType, 22);
    //
    //     $handle = fopen($thisFltFileAbs, "a");
    //
    //     // write header
    //     // fputcsv($handle, $header, "\t");
    //
    //     $rowCount = rand(5, 100);
    //
    //     for ($pos = 0; $pos <= $rowCount; $pos++) {
    //       $randomArr = [];
    //       array_push($randomArr, $recordType); // record type
    //       array_push($randomArr, $this->generateRandomID(40)); // Constituent ID
    //       array_push($randomArr, $this->generateRandomID(10)); // Casework ID
    //       array_push($randomArr, $this->generateRandomID(10)); // 3D Sequence Nuber
    //       array_push($randomArr, $this->faker->randomElement($array = array ('CM',''))); // Text Type
    //       array_push($randomArr, $this->faker->text(rand(150, 5000))); // Casework Text
    //       fputcsv($handle, $randomArr, "\t");
    //     }
    //     fclose($handle);
    //
    //     //$fieldTypes = (new CSVHelper)->determineTypes(false, $testFilesFolder.'/'.$thsFltFile, 100);
    //     //(new TableHelper)->createTable($testFilesFolder, $thsFltFile, $recordType.'testing', $header, $fieldTypes, 1);
    //
    //     //$this->assertTrue(Schema::hasTable($recordType.'testing'));
    //
    //     // drop test table
    //     //Schema::dropIfExists($recordType);
    //
    //     //unlink($thisFltFileAbs);
    //
    //     // clear folder that was created with the table
    //     //rmdir($filePath.'/'.$recordType);
    // }

    // public function testCreate3DCMSfiles() {
    //     // location of test files
    //     $filePath = './storage/app';
    //     $testFilesFolder = 'files/test';
    //     $recordType = "4E";
    //
    //     $thsFltFile = $recordType . '-random.tab';
    //     $thisFltFileAbs = $filePath.'/'.$testFilesFolder.'/'.$thsFltFile;
    //     touch($thisFltFileAbs);
    //
    //     // get header from database
    //     $header = (new CMSHelper)->getCMSFields($recordType, 5);
    //
    //     $handle = fopen($thisFltFileAbs, "a");
    //
    //     // write header
    //     // fputcsv($handle, $header, "\t");
    //
    //     $rowCount = rand(5, 100);
    //
    //     for ($pos = 0; $pos <= $rowCount; $pos++) {
    //       $randomArr = [];
    //       array_push($randomArr, $recordType); // record type
    //       array_push($randomArr, $this->generateRandomID(40)); // Constituent ID
    //       array_push($randomArr, $this->generateRandomID(10)); // Casework ID
    //       array_push($randomArr, $this->generateRandomID(10)); // Transaction ID
    //       array_push($randomArr, $this->faker->word); // Merge Field Name
    //       array_push($randomArr, $this->faker->text(rand(151, 499))); // Casework Text
    //       fputcsv($handle, $randomArr, "\t");
    //     }
    //     fclose($handle);
    //
    //     //$fieldTypes = (new CSVHelper)->determineTypes(false, $testFilesFolder.'/'.$thsFltFile, 100);
    //     //(new TableHelper)->createTable($testFilesFolder, $thsFltFile, $recordType.'testing', $header, $fieldTypes, 1);
    //
    //     //$this->assertTrue(Schema::hasTable($recordType.'testing'));
    //
    //     // drop test table
    //     //Schema::dropIfExists($recordType);
    //
    //     //unlink($thisFltFileAbs);
    //
    //     // clear folder that was created with the table
    //     //rmdir($filePath.'/'.$recordType);
    // }
}
