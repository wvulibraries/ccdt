<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Adapters;

use Illuminate\Support\Facades\DB;
use App\Models\Collection;
use App\Models\Table;
use App\Helpers\CSVHelper;
use App\Helpers\CustomStringHelper;

use Log;

/**
 * Import Adapter
 *
 * The Import Adapter is used in the Jobs/FileImport.php 
 * processes a flatfile and imports the contents into a database table.
 * It performs various functions on each line to insure each row is read
 * correctly and imported into the table.
 * 
 * @param string $tblNme
 * @param string $fltFlePath
 * @param string $fltFleNme
 * 
 * @return array of field headers
 */

class ImportAdapter {  
    public $tkns;

    // Class Variables
    private $tblNme;
    private $fltFlePath;
    private $fltFleNme;

    private $lastErrRow;
    private $savedTkns;
    private $orgCount;
    private $delimiter;

    private $collection;
    private $table;
    private $tableFields;
    private $recordsToInsert;
    private $prcssd;
    private $currentRecord;

    // Helpers
    private $csvHelper;

    // Class Constants
    const FIELD_LIMITER_VALUE = 2000;
    const DEFAULT_FIELD_COUNT = 32;

    public function __construct($tblNme, $fltFlePath, $fltFleNme) {

      // Set items
      $this->tblNme = $tblNme;
      $this->fltFlePath = $fltFlePath;
      $this->fltFleNme = $fltFleNme;      

      // Init Class Variables
      $this->lastErrRow = NULL;
      $this->savedTkns = NULL;

      // Init Helpers
      $this->csvHelper = new CSVHelper;
      
      //get table
      $this->table = Table::where('tblNme', $this->tblNme)->first();

      // find the collection
      $this->collection = Collection::findorFail($this->table->collection_id);

      // set table fields
      $this->tableFields = $this->getTableFields();

      // determine number of fields without the srchIndex
      $this->orgCount = count($this->tableFields) - 1;

      // Temporary store of items to be inserted into the table
      $this->recordsToInsert = [];

      // Counter for processed
      $this->prcssd = 0;
    }    

     /**
     * Merges $this->savedTkns and $this->tkns 
     * saves merged array to $this->tkns
     * 
     * Designed around the Rockefeller data their was instances where a
     * incorrect character caused a break in reading the line. When this 
     * is detected due to a inconsistent field count we will attempt to 
     * merge the 2 lines.
     */    
    public function mergeLines() : void {
        $numItem = count($this->savedTkns) - 1;
        $this->savedTkns[ $numItem ] = $this->savedTkns[ $numItem ] . ' ' . $this->tkns[ 0 ];
        unset($this->tkns[ 0 ]);
        $this->tkns = ( (count($this->tkns) > 0) ? array_merge($this->savedTkns, $this->tkns) : $this->savedTkns );
 
        // clear last saved line since we did a merge
        $this->lastErrRow = NULL;
        $this->savedTkns = NULL;        
    }

    /**
     * takes a string and prepares it to be used inserted as a new record
     * if we do not find enough items in the line we will check and see if
     * the previous line was saved and merge them and check the count again.
     * if their is insufficent items we will save the tkns and the row position
     * so we can attempt a merge later.
     * 
     * @param string $curLine current line read from the file
     * @return boolean
     */
    public function prepareLine($curLine) : bool {
        // Strip out Quotes that are sometimes seen in csv files around each item
        $curLine = str_replace('"', "", $curLine);

        // Tokenize the line
        $this->tkns = $this->csvHelper->tknze($curLine, $this->delimiter);

        // Validate the tokens and filter them
        $this->tkns = $this->csvHelper->fltrTkns($this->tkns);

        // if lastErrRow is the previous row try to combine the lines
        if ((count($this->tkns) != $this->orgCount) && ($this->lastErrRow == $this->prcssd) && ($this->savedTkns != NULL)) {
            $this->mergeLines();
        }

        // if token count doesn't match what is exptected save the tkns and last row position
        if (is_array($this->tkns) && (count($this->tkns) != $this->orgCount)) {
          // save the last row position and the Tokenized row
          $this->lastErrRow = $this->prcssd;
          $this->savedTkns = $this->tkns;
          return false;
        }

        return true;
    }

    /**
    * takes array of tokens. Creates a search index and 
    * inserts them into the table
    * 
    * @return boolean
    */   
    public function processLine() : bool  {
        if(!is_array($this->tkns) && empty($this->tkns)) { return false; }

        // verify that $tkns match the expected field count
        if (count($this->tkns) == $this->orgCount) {
          // Declare an array
          $this->currentRecord = array();

          // Compact them into one array with utf8 encoding
          for ($i = 0; $i<$this->orgCount; $i++) {
            $this->currentRecord[ strval($this->tableFields[ $i ]) ] = utf8_encode($this->tkns[ $i ]);
          }

          return true;
        }

        return false;
    }

    /**
     * Process employs following algorithm:
     * get all the column names from table name
     * 1. Read the file as spl object
     * 2. For each line
     *   1. Validate 
     */
    public function process() {
      // 1. Read the file as spl object
      $fltFleFullPth = storage_path('app/'.$this->fltFlePath.'/'.$this->fltFleNme);

      // Create an instance for the file
      $curFltFleObj = new \SplFileObject($fltFleFullPth);

      // Detect delimiter used in file
      $this->delimiter = $this->csvHelper->detectDelimiter($fltFleFullPth);
      
      // Check for an empty file
      if (filesize($fltFleFullPth)>0) {

        // Ignore the first line if the collection is cms
        if ($this->collection->isCms == false) { $curFltFleObj->seek(1); }

        // number of records to insert based on field count
        $insertCount = self::FIELD_LIMITER_VALUE * (self::DEFAULT_FIELD_COUNT / $this->orgCount);

        // For each line
        while ($curFltFleObj->valid()) {
          // Call prepareLine to process the next line of the file
          if ($this->prepareLine($curFltFleObj->current())) {
            // process $this->tkns 
            $this->processLine();

            // saves $this->curArry to the $this->recordsToInsert array. 
            $this->queueRecord();

            // insert records once we reach insert count
            if (count($this->recordsToInsert) >= $insertCount) {
              //insert Record(s) into database
              $result = $this->table->insertRecord($this->recordsToInsert);

              // clear $this->recordsToInsert array
              $this->recordsToInsert = [];
            }
          }

          $curFltFleObj->next();
        }

        // insert records at the end of file
        if (count($this->recordsToInsert ) > 0) {
            //insert Record(s) into database
            $this->table->insertRecord($this->recordsToInsert);
        }  

      }
      else {
        throw new \Exception("Cannot Import a Empty File.");
      }
    }

    /**
    * takes current line that was processed and saves it to array
    * to be inserted into the database.
    *
    * @return boolean
    */     
    private function queueRecord() : bool {
      // save line to be inserted later
      if ($this->currentRecord) {
        // add row to data array to insert it later
        array_push($this->recordsToInsert, $this->currentRecord);

        // Update the counter if the line was inserted
        $this->prcssd += 1;

        return true;
      } 

      return false;
    }

    /**
    * get current column list from current table
    * remove common fields id and time stamps
    * return remaining fields as an array
    * @return array
    */ 
    private function getTableFields() : array {
      // Get Column List from table
      $clmnLst = $this->table->getColumnList();

      // remove the id and time stamps
      // return the remaining items in array
      return array_splice($clmnLst, 1, count($clmnLst) - 3);
    }

}