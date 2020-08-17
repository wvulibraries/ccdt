<?php
  # app/tests/Unit/Models/TableUnitTest.php

  use App\Helpers\CollectionHelper;
  use App\Models\Collection;
  use App\Models\Table;

  class TableUnitTest extends TestCase
  {
    private $colName;
    private $tableName;  

    public function setUp(): void {
      parent::setUp();

      // Generate Collection Name
      $this->colName = $this->testHelper->generateCollectionName();

      // Generate Table Name
      $this->tableName = $this->testHelper->createTableName();              
    }

    protected function tearDown(): void {
      // test tables, files and folders that were created
      $this->testHelper->cleanupTestTablesAndFiles();

      // Delete Test Collections
      $this->testHelper->deleteTestCollections();         

      parent::tearDown();
    } 

    public function testTableHasField() {
      $this->testHelper->createCollectionWithTable($this->colName, $this->tableName);

      // get newly created table
      $table = Table::where('tblNme', $this->tableName)->first();

      // getColumnType returns a array containing type and size of the field
      $fieldType = $table->getColumnType('firstname');

      // Field should be text
      $this->assertEquals($fieldType['type'], 'text');      
      
      // Field doesn't exist should return false
      $this->assertFalse($table->getColumnType('missingField'));        
    }

  }
?>
