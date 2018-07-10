<?php

namespace App\Libraries;

class CSVHelper {
    /**
     * Table Helper
     *
     * These are various functions that help with dynamically
     * creating tables that can be searched.
     *
     */

     /*
     * @param string $csvFile Path to the CSV file
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
      */
     public function checkFile($header, $fltFleAbsPth, $readCount) {
       $checkArray = [];
       $file = new \SplFileObject(\storage_path()."/app/".$fltFleAbsPth);
       $file->setFlags(\SplFileObject::DROP_NEW_LINE);
       $count = 0;
       if ($header) {
         // skip header
         $file->seek(1);
       }
       while ($file->valid()) {
           // break while if we reach loop maximum read
           if ($count > $readCount) {
             break;
           }
           ++$count;

           // read line
           $line = $file->fgets();

           // Strip out Quotes that are sometimes seen in csv files around each item
           $hdr = str_replace('"', "", $line);

           // Tokenize the line
           $tkns = $this->tknze($hdr, $this->detectDelimiter(\storage_path()."/app/".$fltFleAbsPth));
           //var_dump($tkns);
           // Validate the tokens and filter them
           $tkns = $this->fltrTkns($tkns);

           $fieldcount = count($tkns);
           if (count($checkArray) != $fieldcount) {
             $checkArray = [];
             for ($x = 0; $x < $fieldcount; $x++) {
               // push default item as numeric
               array_push($checkArray, [1, 0]);
             }
           }

           foreach($tkns as $x=>$x_value)
             {
               // change type if we detect any that the string isn't numeric
               if (!is_numeric($x_value) && ($x_value != "")) {
                 $checkArray[$x][0] = 0;
               }
               // save character count if higher than last pass
               if ($checkArray[$x][1] < strlen($x_value)) {
                 $checkArray[$x][1] = strlen($x_value);
               }
             }
       }
       return ($checkArray);
     }

     public function determineTypes($header, $fltFleAbsPth, $readCount) {
         // call checkFile process the passed file
         $checkArray = $this->checkFile($header, $fltFleAbsPth, $readCount);

         $fieldType = [];
         // determine final field types
         foreach($checkArray as $x=>$x_value)
           {
             if ($x_value[0] == 0) {
               switch ($x_value[1]) {
                   case 0:
                   case $x_value[1] < 50:
                       array_push($fieldType, ['string', 'default']);
                       break;
                   case $x_value[1] < 150:
                       array_push($fieldType, ['string', 'medium']);
                       break;
                   case $x_value[1] < 500:
                       array_push($fieldType, ['string', 'big']);
                       break;
                   case $x_value[1] < 65535:
                       array_push($fieldType, ['text', 'default']);
                       break;
                   case $x_value[1] < 16777215:
                       array_push($fieldType, ['text', 'medium']);
                       break;
                   case $x_value[1] < 4294967295:
                       array_push($fieldType, ['text', 'big']);
               }
             }

             if ($x_value[0] == 1) {
               switch ($x_value[1]) {
                   case $x_value[1] >= 1000:
                       array_push($fieldType, ['integer', 'big']);
                       break;
                   case $x_value[1] >= 100:
                       array_push($fieldType, ['integer', 'medium']);
                       break;
                   default:
                       array_push($fieldType, ['integer', 'default']);
               }
             }

           }
         return $fieldType;
     }
}
