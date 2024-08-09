<?php

use App\Helpers\CollectionHelper;
use App\Helpers\CMSHelper;

class CMSHelperTest extends BrowserKitTestCase
{
  public $cmsHelper;
  public $collectionHelper;

  private $colName;     

  public function setUp(): void {
    parent::setUp();
    $this->cmsHelper = new CMSHelper;  
    $this->collectionHelper = new CollectionHelper;

    // Generate Collection Name
    $this->colName = $this->testHelper->generateCollectionName();  
  }

  protected function tearDown(): void {
      // test tables, files and folders that were created
      $this->testHelper->cleanupTestTables();

      // Delete Test Collections
      $this->testHelper->deleteTestCollections();         

      parent::tearDown();
  } 

    public function testgetCMSFields1A() {
      // Create Test Collection        
      $collection = $this->testHelper->createCollection($this->colName, 1, true);  

      $fieldType = '1A';
      $header = array('Record Type', 'Constituent ID', 'Individual Type', 'Prefix', 'First Name', 'Middle Name', 'Last Name', 'Suffix', 'Appellation', 'Salutation', 'Date of Birth', 'No Mail Flag', 'Deceased Flag');
      $response = $this->cmsHelper->getCMSFields($collection->id, $fieldType, count( (array) $header));

      // compare with the $header to verify we have what we expected
      $this->assertEquals($response, $header);
      $this->assertEquals(count( (array) $response), count( (array) $header));
    }
    
    public function testgetCMSFields1BWithCMSType() {
      // Create Test Collection        
      $collection = $this->testHelper->createCollection($this->colName, 1, true, 2); 
      
      // verify cmsId is set to 2
      $this->assertEquals($collection->cmsId, 2);
      
      $fieldType = '1B';
      $header = array('Record Type', 'Person ID', 'Address ID', 'Address Type', 'Primary Flag', 'Default Address Flag', 'Title', 'Organization Name', 'Address line 1', 'Address line 2', 'Address line 3', 'Address line 4', 'City', 'State', 'Zip Code', 'Carrier Route', 'County', 'Country', 'District', 'Precinct', 'No Mail Flag', 'Deliverability');
      $response = $this->cmsHelper->getCMSFields($collection->id, $fieldType, count( (array) $header));

      // compare with the $header to verify we have what we expected
      $this->assertEquals($response, $header);
      $this->assertEquals(count( (array) $response), count( (array) $header));  
    }      

    public function testgetCMSFields1B() {
      // Create Test Collection        
      $collection = $this->testHelper->createCollection($this->colName, 1, true);  

      $fieldType = '1B';
      $header = array('Record Type', 'Constituent ID', 'Address ID', 'Address Type', 'Primary Flag', 'Default Address Flag', 'Title', 'Organization Name', 'Address line 1', 'Address line 2', 'Address line 3', 'Address line 4', 'City', 'State', 'Zip Code', 'Carrier Route', 'County', 'Country', 'District', 'Precinct', 'No Mail Flag', 'Agency Code');
      $response = $this->cmsHelper->getCMSFields($collection->id, $fieldType, count( (array) $header));

      // in our record types 2 1B records exist both with 22 fields
      // function cannot determine which to use so null is returned.
      $this->assertNull($response);
    }       

    public function testCreateCmsHeader() {
        // Create Test Collection        
        $collection = $this->testHelper->createCollection($this->colName, 1, true);     
        
        // Random Field Count
        $fieldCount = rand(1, 50);

        // Pass null to force the function to generate a generic header
        $header = $this->cmsHelper->cmsHeader($collection->id, null, $fieldCount);

        // Verify Array Items Match $fieldCount
        $this->assertEquals(count($header), $fieldCount); 

        // Verify First Array Item is correct
        $this->assertEquals($header[0], 'Field0');
    }

}
