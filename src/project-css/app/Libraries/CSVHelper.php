<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Storage;

class CSVHelper {
    /**
     * Table Helper
     *
     * These are various functions that help with processing
     * csv or other delmimited files prior to importing.
     *
     */

     /**
      * Determine delimiter used in a file
      *
      * @param string $csvFile Path to the CSV file
      *
      * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
      * @return string Delimiter
      */
     public function detectDelimiter($csvFile)
     {
         $delimiters = array(
             ';' => 0,
             ',' => 0,
             "\t" => 0,
             "|" => 0
         );

         $handle = fopen($csvFile, "r");
         $firstLine = fgets($handle);
         fclose($handle);
         foreach ($delimiters as $delimiter => &$count) {
             $count = count(str_getcsv($firstLine, $delimiter));
         }

         return array_search(max($delimiters), $delimiters);
     }

     /**
     * Method to tokenize the string for multiple lines
     * @param string $line
     * @param false|string $delimiter
     */
     public function tknze($line, $delimiter) {
       // Tokenize the line
       // Define a pattern
       $pattern = '/['.$delimiter.']/';

       // preg split
       $tkns = preg_split($pattern, $line);

       // Return the array
       return $tkns;
     }

     /**
     * Method to check if the given tkns are null
     */
     public function fltrTkns($tkns) {
       // Run through the files
       foreach ($tkns as $key => $tkn) {
         // trim the token
         $tkns[ $key ] = trim($tkn);
       }

       // Return the filtered tokens
       return $tkns;
     }

     /**
      * Returns Multideminsional Array
      * function determines if the field is numeric or string type
      * along with the max character count detected in the column
      *
      * @param boolean $header true if file has header row
      * @param string $fltFleAbsPth location of file to be checked.
      * @param integer $readCount lines to read to determine type
      *
      * @author Tracy A. McCormick <tam0013@mail.wvu.edu>
      * @return Array that contains type and character count for each detected field
      */
     public function checkFile($hasheader, $fltFleAbsPth, $readCount) {
       // 1. Get the flatfile instance
       // Check if the file exists
       if (!Storage::has($fltFleAbsPth)) {
         // If the file doesn't exists return with error
         return false;
       }
       // Create an instance for the file
       $fltFleObj = new \SplFileObject(\storage_path()."/app/".$fltFleAbsPth);

       // 2. Validate the file type
       // Create a finfo instance
       $fleInf = new \finfo(FILEINFO_MIME_TYPE);

       // Get the file type
       $fleMime = $fleInf->file($fltFleObj->getRealPath());

       // Check the mimetype
       if (!str_is($fleMime, "text/plain")) {
         // If the file isn't a text file return false
         return false;
       }

       // Check if the file is empty
       if (!$this->isEmpty($fltFleObj)>0) {
         return false;
       }

       $hasheader ? $fltFleObj->seek(1) : $fltFleObj->seek(0);

       $checkArray = [];
       $count = 0;
       $delimiter = $this->detectDelimiter(\storage_path()."/app/".$fltFleAbsPth);

       // 3. Loop over the file until eof or $readCount is reached
       while ($fltFleObj->valid()) {
         // break while if we reach loop maximum read
         if ($count > $readCount) {
           break;
         }
         ++$count;

         $hdr = $fltFleObj->fgets();

         // Strip out Quotes that are sometimes seen in header rows of csv files
         $hdr = str_replace('"', "", $hdr);

         // Tokenize the line
         $tkns = $this->tknze($hdr, $delimiter);

         // Validate the tokens and filter them
         $tkns = $this->fltrTkns($tkns);

         $fieldcount = count($tkns);
         if (count($checkArray) < $fieldcount) {
           for ($pos = count($checkArray); $pos < $fieldcount; $pos++) {
             // push default item as numeric
             array_push($checkArray, [0, 0]);
           }
         }

         foreach($tkns as $x=>$x_value) {
           // change type if we detect any that the string isn't numeric
           if (is_numeric($x_value) && ($x_value != "")) {
             $checkArray[$x][0] = 1;
           }
           // save character count if higher than last pass
           if ($checkArray[$x][1] < strlen($x_value)) {
             $checkArray[$x][1] = strlen($x_value);
           }
         }

       }
       // Returning detected fields
       return $checkArray;
     }

     public function determineTypes($hasheader, $fltFleAbsPth, $readCount) {
         // call checkFile process the passed file
         $checkArray = $this->checkFile($hasheader, $fltFleAbsPth, $readCount);

         $fieldType = [];

         // determine final field types
         foreach($checkArray as $x=>$x_value)
         {
           // if integer is detected and character count is greater than
           // 10 we will store it as text
           if (($x_value[0] == 1) && ($x_value[1] > 10)) {
             $x_value[0] = 0;
           }

           if ($x_value[0] == 0) {
             switch ($x_value[1]) {
                 case 0:
                 case $x_value[1] < 30:
                     array_push($fieldType, ['string', 'default', $x_value[1]]);
                     break;
                 case $x_value[1] < 150:
                     array_push($fieldType, ['string', 'medium', $x_value[1]]);
                     break;
                 case $x_value[1] < 500:
                     array_push($fieldType, ['string', 'big', $x_value[1]]);
                     break;
                 case $x_value[1] < 2000:
                     array_push($fieldType, ['text', 'default', $x_value[1]]);
                     break;
                 case $x_value[1] < 8000:
                     array_push($fieldType, ['text', 'medium', $x_value[1]]);
                     break;
                 default:
                     array_push($fieldType, ['text', 'big', $x_value[1]]);
             }
           }
           elseif ($x_value[0] == 1) {
             switch ($x_value[1]) {
                 case $x_value[1] <= 2:
                   array_push($fieldType, ['integer', 'default', $x_value[1]]);
                   break;
                 case $x_value[1] <= 7:
                   array_push($fieldType, ['integer', 'medium', $x_value[1]]);
                   break;
                 default:
                     array_push($fieldType, ['integer', 'big', $x_value[1]]);
             }
           }
       }

       return $fieldType;
     }

     public function generateHeader($fieldCount) {
       $header = array();
       for ($pos = 0; $pos < $fieldCount; $pos++) {
         array_push($header, 'field'.$pos);
       }
       return $header;
     }

     // function is used to adjust detected types to match
     // what is expected from $header
     public function adjustTypes($fieldTypes, $header) {
       // remove extra field that is sometimes detected
       if ((count($fieldTypes)-1) == count($header) && ($fieldTypes[count($fieldTypes)-1][2] == 0)) {
         array_pop($fieldTypes);
       }
       elseif (count($fieldTypes) < count($header)) {
         // add extra fields
         for ($pos = count($fieldTypes); $pos < count($header); $pos++) {
           // add extra text fields if the header
           // has more than what is detected
           array_push($fieldTypes, ['text', 'big', 0]);
         }
       }
       return $fieldTypes;
     }

     /**
     * Get the line numbers for a fileobject
     * @param \SplFileObject $fltFleObj
     */
     public function isEmpty($fltFleObj) {
       // Before anything set to seek first line
       $fltFleObj->seek(0);

       // Variable to count the length
       $len = 0;

       // Loop till EOF
       while (!$fltFleObj->eof()) {
         // Check if the file has at least one line
         if ($len>=1) {
           // Break here so that it's not reading huge files
           break;
         }
         // Increament variable
         $len += 1;
         // Seek the next item
         $fltFleObj->next();
       }

       // Return the length
       return $len;
     }

     /**
     * Method to validate the file type and read the first line only for schema
     * Algorithm:
     * 1. Get the flatfile instance
     * 2. Validate the file type
     * 3. Tokenize the current line
     * 4. Validate the tokens and return
     * 5. Return first line as array
     * @param string $fltFlePth
     * @return boolean
     */
     public function schema($fltFlePth) {
       // 1. Get the flatfile instance
       // Check if the file exists
       if (!Storage::has($fltFlePth)) {
         // If the file doesn't exists return with error
         return false;
       }
       // Create an instance for the file
       $fltFleObj = new \SplFileObject(\storage_path()."/app/".$fltFlePth);

       // 2. Validate the file type
       // Create a finfo instance
       $fleInf = new \finfo(FILEINFO_MIME_TYPE);
       // Get the file type
       $fleMime = $fleInf->file($fltFleObj->getRealPath());
       // Check the mimetype
       if (!str_is($fleMime, "text/plain")) {
         // If the file isn't a text file return false
         return false;
       }
       // Check if the file is empty
       if (!$this->isEmpty($fltFleObj)>0) {
         return false;
       }

       // 3. Tokenize the current line
       // Get the first line as the header
       $fltFleObj->seek(0);
       $hdr = $fltFleObj->fgets();

       // Strip out Quotes that are sometimes seen in header rows of csv files
       $hdr = str_replace('"', "", $hdr);

       // Tokenize the line
       $tkns = $this->tknze($hdr, $this->detectDelimiter(\storage_path()."/app/".$fltFlePth));
       // Validate the tokens and filter them
       $tkns = $this->fltrTkns($tkns);

       // Returning tokens
       return $tkns;
     }
}
