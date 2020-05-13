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
    public $savedTkns;
    public $lastErrRow;

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
        // setup helper instances
        $csvHelper = (new CSVHelper);
        $stringHelper = (new customStringHelper);

        // Strip out Quotes that are sometimes seen in csv files around each item
        $curLine = str_replace('"', "", $curLine);

        // Tokenize the line
        $tkns = $csvHelper->tknze($curLine, $delimiter);

        // Validate the tokens and filter them
        $tkns = $csvHelper->fltrTkns($tkns);

        // if lastErrRow is the previous row try to combine the lines
        if ((count($tkns) != $orgCount) && ($this->lastErrRow == $prcssd - 1) && ($this->savedTkns != NULL)) {
            $tkns = $stringHelper->mergeLines($this->savedTkns, $tkns);

            // clear last saved line since we did a merge
            $this->lastErrRow = NULL;
            $this->savedTkns = NULL;
        }

        // if token count doesn't match what is exptected save the tkns and last row position
        if (count($tkns) != $orgCount) {
          // save the last row position and the Tokenized row
          $this->lastErrRow = $prcssd;
          $this->savedTkns = $tkns;
          return (null);
        }

        return ($tkns);
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
    public function processLine($tkns, $orgCount, $clmnLst) {
        if(!is_array($tkns) && empty($tkns)) { return false; }

        // verify that passed $tkns match the expected field count
        if (count($tkns) == $orgCount) {
          // Declae an array
          $curArry = array();

          // Compact them into one array with utf8 encoding
          for ($i = 0; $i<$orgCount; $i++) {
            $curArry[ strval($clmnLst[ $i ]) ] = utf8_encode($tkns[ $i ]);
          }

          // add srchindex
          $curArry[ 'srchindex' ] = (new customStringHelper)->createSrchIndex(implode(" ", $tkns));

          // return new record to be inserted
          return $curArry;
        }
        return false;
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
      //get table
      $table = Table::where('tblNme', $tblNme)->first();

      // find the collection
      $thisClctn = Collection::findorFail($table->collection_id);

      $clmnLst = $table->getColumnList();

      // remove the id and time stamps
      $clmnLst = array_splice($clmnLst, 1, count($clmnLst) - 3);

      // determine number of fields without the srchIndex
      $orgCount = count($clmnLst) - 1;

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

        // For each line
        while ($curFltFleObj->valid()) {
          // Call prepareLine to process the next line of the file
          $tkns = $this->prepareLine($curFltFleObj->current(), $delimiter, $orgCount, $prcssd);
        
          // process $tkns 
          $curArry = $this->processLine($tkns, $orgCount, $clmnLst);

          // save line to be inserted later
          if ($curArry) {
            // add row to data array to insert it later
            array_push($data, $curArry);

            // Update the counter if the line was inserted
            $prcssd += 1;
          }

          // insert records once we reach 500
          if (count($data) >= 1000) {
            //insert Record into database
            $table->insertRecord($data);

            // clear $data array
            $data = [];
          }

          $curFltFleObj->next();
        }

        // insert records at the end of file
        if (count($data) > 0) {
            //insert Record into database
            $table->insertRecord($data);
        }        

      }
      else {
        throw new \Exception("Cannot Import a Empty File.");
      }
    }

    public function mysql_load($tblNme, $fltFlePath, $fltFleNme, $ignoreFirst = true) {   
        $fltFleAbsPth = $fltFlePath.'/'.$fltFleNme;
        $fltFleFullPth = storage_path('app/'.$fltFleAbsPth);

        // Detect delimiter used in file
        $delimiter = (new CSVHelper)->detectDelimiter($fltFleFullPth);

        //get table
        $table = Table::where('tblNme', $tblNme)->first();

        // find the collection
        $thisClctn = Collection::findorFail($table->collection_id);

        if ($thisClctn->isCms == false) {
            $statement = <<<eof
                LOAD DATA INFILE '$fltFleFullPth' INTO TABLE '$tblNme'
                FIELDS TERMINATED BY '$delimiter'
                IGNORE 1 LINES            
            eof;
        }
        else {
            $statement = <<<eof
                LOAD DATA INFILE '$fltFleFullPth' INTO TABLE '$tblNme'
                FIELDS TERMINATED BY '$delimiter'            
            eof;
        }

        return DB::connection()->statement($statement);        
    }

}