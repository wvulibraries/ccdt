<?php

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
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
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
    
    /**
     * Constructor to setup class items
     * 
     * @param string $tblNme
     * @param string $fltFlePath
     * @param string $fltFleNme
     *
     * @author Tracy A McCormick <tam0013@mail.wvu.edu>
     */ 
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
     * Returns the result of merging $saved and $current WITHOUT mutating
     * any instance state. The caller is responsible for validating the
     * result before committing to it.
     *
     * Designed around the Rockefeller data their was instances where a
     * incorrect character caused a break in reading the line. When this
     * is detected due to a inconsistent field count we will attempt to
     * merge the 2 lines.
     *
     * @param array $saved   tokens saved from the previous (short/long) line
     * @param array $current tokens from the current line
     *
     * @author Tracy A McCormick <tam0013@mail.wvu.edu>
     *
     * @return array
     */
    private function mergeLines(array $saved, array $current) : array {
        $current = array_values($current);
        $numItem = count($saved) - 1;
        $saved[ $numItem ] = $saved[ $numItem ] . ' ' . ($current[ 0 ] ?? '');
        unset($current[ 0 ]);
        return ( (count($current) > 0) ? array_merge($saved, $current) : $saved );
    }

    /**
     * takes a string and prepares it to be used inserted as a new record.
     *
     * Legacy flat-file exports (like the IQ/CMS exports this importer
     * targets) commonly drop trailing empty/blank delimited columns rather
     * than writing an empty field, so a short row is far more often a
     * legitimate record with blank trailing fields than a record whose
     * value wrapped onto the next line. To avoid corrupting the import we:
     *   1. Skip lines that contain no delimiter at all (blank lines and
     *      stray comment/section-separator rows some exports include).
     *   2. Only merge the current line onto a previously saved short/long
     *      line when doing so produces EXACTLY the expected field count -
     *      never accept a merge that still doesn't line up, since that is
     *      what causes unrelated rows to get glued together and cascades
     *      into corrupting the rest of the file.
     *   3. If a row simply has fewer fields than expected and no valid
     *      merge applies, pad the missing trailing fields as blank instead
     *      of guessing at a merge.
     *   4. If a row has MORE fields than expected, save it in case the
     *      next line's remainder completes it (a genuine wrapped value);
     *      otherwise log it for manual review rather than silently
     *      dropping or corrupting data.
     *
     * @param string $curLine current line read from the file
     *
     * @author Tracy A McCormick <tam0013@mail.wvu.edu>
     *
     * @return boolean
     */
    private function prepareLine($curLine) : bool {
        // Strip out Quotes that are sometimes seen in csv files around each item
        $curLine = str_replace('"', "", $curLine);

        // Tokenize the line
        $this->tkns = $this->csvHelper->tknze($curLine, $this->delimiter);

        // Validate the tokens and filter them
        $this->tkns = $this->csvHelper->fltrTkns($this->tkns);

        // Lines with no delimiter at all carry no usable data (blank
        // lines, or stray comment/section-separator rows some legacy
        // exports include between blocks of records) - skip them without
        // disturbing any pending merge state.
        if (count($this->tkns) <= 1) {
            return false;
        }

        // If the previous line was saved as short/long, only accept a
        // merge with the current line if it resolves to exactly the
        // expected field count.
        if (($this->savedTkns !== NULL) && ($this->lastErrRow == $this->prcssd)) {
            $merged = $this->mergeLines($this->savedTkns, $this->tkns);

            if (count($merged) === $this->orgCount) {
                $this->tkns = $merged;
                $this->lastErrRow = NULL;
                $this->savedTkns = NULL;
                return true;
            }

            // Merge didn't line up cleanly - don't keep growing a bad
            // blob. Log the abandoned row and evaluate the current line
            // on its own merits below.
            Log::warning("Import for table {$this->tblNme}: could not reconcile saved row with next line, dropping saved row: " . json_encode($this->savedTkns));
            $this->lastErrRow = NULL;
            $this->savedTkns = NULL;
        }

        $tknCount = count($this->tkns);

        if ($tknCount === $this->orgCount) {
            return true;
        }

        if ($tknCount < $this->orgCount) {
            // Treat missing trailing fields as blank rather than assuming
            // a broken multi-line record - this is the common case for
            // these exports and avoids corrupting subsequent rows.
            $this->tkns = array_pad($this->tkns, $this->orgCount, '');
            return true;
        }

        // More fields than expected - likely an unescaped delimiter inside
        // a value, or the start of a record whose final field wraps onto
        // the next line. Save it so the next line can be checked for a
        // possible merge.
        Log::warning("Import for table {$this->tblNme}: row had {$tknCount} fields, expected {$this->orgCount}. Saved pending merge check: " . json_encode($this->tkns));
        $this->lastErrRow = $this->prcssd;
        $this->savedTkns = $this->tkns;
        return false;
    }

    /**
    * takes array of tokens. Creates a search index and 
    * inserts them into the table
    * 
    * @author Tracy A McCormick <tam0013@mail.wvu.edu>
    *    
    * @return boolean
    */   
    private function processLine() : bool  {
        if(!is_array($this->tkns) && empty($this->tkns)) { return false; }

        // Declare an array
        $this->currentRecord = array();

        // Compact them into one array with utf8 encoding
        for ($i = 0; $i<$this->orgCount; $i++) {
          $this->currentRecord[ strval($this->tableFields[ $i ]) ] = utf8_encode($this->tkns[ $i ]);
        }

        return true;

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

        // if a row was still pending reconciliation when we hit end of
        // file, it was never imported - log it so it isn't silently lost.
        if ($this->savedTkns !== NULL) {
            Log::warning("Import for table {$this->tblNme}: file ended with an unresolved row, not imported: " . json_encode($this->savedTkns));
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
    * @author Tracy A McCormick <tam0013@mail.wvu.edu>
    *
    * @return void
    */     
    private function queueRecord() {
      // add row to recordsToInsert array for batch insert
      array_push($this->recordsToInsert, $this->currentRecord);

      // Update the counter
      $this->prcssd += 1;
    }

    /**
    * get current column list from current table
    * remove common fields id and time stamps
    * return remaining fields as an array
    *
    * @author Tracy A McCormick <tam0013@mail.wvu.edu>
    *    
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