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

class ImportAdapter {  
    public $tkns;
    public $curArry;
    public $clmnLst;
    public $tblNme;

    // Class Variables
    private $errorCount;
    private $mergeCount;
    private $lastErrRow;
    private $savedTkns;

    public function __construct()
    {
      // Init Class Variables
      $this->errorCount = 0;
      $this->mergeCount = 0;

      $this->lastErrRow = NULL;
      $this->savedTkns = NULL;
    }    

     /**
     * Takes 2 arrays of tokens and merges them.
     * Designed around the Rockefeller data their was instances where a
     * incorrect character caused a break in reading the line. When this 
     * is detected due to a inconsistent field count we will attempt to 
     * merge the 2 lines.
     * @param array $tkns1
     * @param array $tkns2
     * @return array
     */    
    public function mergeLines() {
        $numItem = count($this->savedTkns) - 1;
        $this->savedTkns[ $numItem ] = $this->savedTkns[ $numItem ] . ' ' . $this->tkns[ 0 ];
        unset($this->tkns[ 0 ]);
        $this->tkns = ( (count($this->tkns) > 0) ? array_merge($this->savedTkns, $this->tkns) : $this->savedTkns );
    }

    /**
     * takes a string and prepares it to be used inserted as a new record
     * if we do not find enough items in the line we will check and see if
     * the previous line was saved and merge them and check the count again.
     * if their is insufficent items we will save the tkns and the row position
     * so we can attempt a merge later.
     * @param string $curLine current line read from the file
     * @param string $delimiter type of delmiter that is used in the file
     * @param integer $orgCount current number of fields expected
     * @param integer $prcssd current position in the file
     * @return string
     */
    public function prepareLine($curLine, $delimiter, $orgCount, $prcssd) {
        // Strip out Quotes that are sometimes seen in csv files around each item
        $curLine = str_replace('"', "", $curLine);

        // Tokenize the line
        $this->tkns = (new CSVHelper)->tknze($curLine, $delimiter);

        // Validate the tokens and filter them
        $this->tkns = (new CSVHelper)->fltrTkns($this->tkns);

        // if lastErrRow is the previous row try to combine the lines
        if ((count($this->tkns) != $orgCount) && ($this->lastErrRow == $prcssd) && ($this->savedTkns != NULL)) {
            $this->mergeLines();

            // clear last saved line since we did a merge
            $this->lastErrRow = NULL;
            $this->savedTkns = NULL;
        }

        // if token count doesn't match what is exptected save the tkns and last row position
        if (is_array($this->tkns) && (count($this->tkns) != $orgCount)) {
          // save the last row position and the Tokenized row
          $this->lastErrRow = $prcssd;
          $this->savedTkns = $this->tkns;
          return false;
        }

        return true;
    }

    /**
    * takes array of tokens. Creates a search index and 
    * inserts them into the table
    * 
    * @param array $tkns array containing all fields of record
    * @param integer $orgCount number of expected fields in the record
    * @param object $clmnLst array containing all field names for table
    * @return boolean
    */   
    public function processLine($orgCount) {
        if(!is_array($this->tkns) && empty($this->tkns)) { return false; }

        // verify that passed $tkns match the expected field count
        if (count($this->tkns) == $orgCount) {
          // Declae an array
          $this->curArry = array();

          // Compact them into one array with utf8 encoding
          for ($i = 0; $i<$orgCount; $i++) {
            $this->curArry[ strval($this->clmnLst[ $i ]) ] = utf8_encode($this->tkns[ $i ]);
          }

        }
    }

    /**
     * Process employs following algorithm:
     * get all the column names from table name
     * 1. Read the file as spl object
     * 2. For each line
     *   1. Validate
     * @param string $tblNme
     * @param string $fltFlePath
     * @param string $fltFleNmetype
     * @param boolean $ignoreFirst 
     * @return void
     */
    public function process($tblNme, $fltFlePath, $fltFleNme) {
      $this->tblNme = $tblNme;

      //get table
      $table = Table::where('tblNme', $tblNme)->first();

      // find the collection
      $thisClctn = Collection::findorFail($table->collection_id);

      $this->clmnLst = $table->getColumnList();

      // remove the id and time stamps
      $this->clmnLst = array_splice($this->clmnLst, 1, count($this->clmnLst) - 3);

      // determine number of fields without the srchIndex
      $orgCount = count($this->clmnLst) - 1;

      // 1. Read the file as spl object
      $fltFleAbsPth = $fltFlePath.'/'.$fltFleNme;
      $fltFleFullPth = storage_path('app/'.$fltFleAbsPth);

      // Create an instance for the file
      $curFltFleObj = new \SplFileObject($fltFleFullPth);

      // Detect delimiter used in file
      $delimiter = (new CSVHelper)->detectDelimiter($fltFleFullPth);
      
      // Check for an empty file
      if (filesize($fltFleFullPth)>0) {

        // Ignore the first line if the collection is cms
        if ($thisClctn->isCms == false) { $curFltFleObj->seek(1); }

        // Counter for processed
        $prcssd = 0;

        // Temporary store of items to be inserted into the
        $data = [];

        // number of records to insert based on field count
        $insertCount = 2000 * (32 / $orgCount);

        // For each line
        while ($curFltFleObj->valid()) {
          // Call prepareLine to process the next line of the file
          if ($this->prepareLine($curFltFleObj->current(), $delimiter, $orgCount, $prcssd)) {
            // process $tkns 
            $this->processLine($orgCount);

            // save line to be inserted later
            if ($this->curArry) {
              // add row to data array to insert it later
              array_push($data, $this->curArry);

              // Update the counter if the line was inserted
              $prcssd += 1;
            } 

            if (count($data) >= $insertCount) {
              //insert Record(s) into database
              $result = $table->insertRecord($data);

              // clear $data array
              $data = [];
            }
          }

          $curFltFleObj->next();
        }

        // insert records at the end of file
        if (count($data) > 0) {
            //insert Record(s) into database
            $table->insertRecord($data);
        }  

      }
      else {
        throw new \Exception("Cannot Import a Empty File.");
      }
    }

}