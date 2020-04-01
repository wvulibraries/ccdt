<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Libraries;

use Illuminate\Support\Facades\DB;
use App\Models\StopWords;
use App\Models\AllowedFileTypes;

class CustomStringHelper {
    /**
     * Custom String Helper
     *
     * These are various functions that help with parsing over
     * strings that that point to files in the storage folder
     * in laravel. These files were orginally in a windows
     * environment.
     *
     */

     // regex patter we will use to detect a social security number
     protected $pattern = '#\b[0-9]{3}-[0-9]{2}-[0-9]{4}\b#';

    /**
     * Separates the string into an array of flatfiles
     * by using explode and the ^ as a separator
     * @param       string  $str    Input string
     * @return      array of strings
     */
    public function separateFiles($str) {
        $filesArray = explode('^', $str);
        return $filesArray;
    }

    /**
     * loop over search array and remove any words that
     * are in the StopWords table.
     * @param       array $search Input array
     * @return      array of strings
     */    
    public function removeCommonWords($search) {
      foreach($search as $key => $word) {
        if (StopWords::isStopWord($word)) {
          unset($search[$key]);
        }
      }
      return $search;
    }

    /**
     * Detects a social security number pattern in string.
     * 
     * @param string $fileContents
     * @return boolean
     */        
    public function ssnExists($fileContents) {
        if ($fileContents != null) {
            // preg_match_all will return a count if it is greater than
            // 0 we have matches against the SSN pattern and will return
            // a true value
            return (preg_match_all($this->pattern, $fileContents, $matches)>0);
        }
        return (false);
    }

    /**
     * If ssnExists returns true we use a preg_replace
     * to replace the social with ###-##-####
     * @param       string  $fileContents    Input string
     * @return      string
     */
    public function ssnRedact($fileContents) {
        if ($this->ssnExists($fileContents)) {
            return (preg_replace($this->pattern, '###-##-####', $fileContents));
        }
        return($fileContents);
    }

    /*
    * @param string $string
    * @return array with filenames
    */
    public function checkForFilenames($string) {
      $foundFiles = [];
      $pieces = explode("/", $string);
      foreach ($pieces as $value) {
        $ext = substr($value, strrpos($value,'.')+1);
        if (AllowedFileTypes::isAllowedType($ext)) {
          array_push($foundFiles, $value);
        }
      }
      return ($foundFiles);
    }

    /**
     * takes a string and prepares it to be used as a search index for fulltext search
     * @param string $curLine
     * @return string
     */
    public function createSrchIndex($curLine) {
      // remove extra characters replacing them with spaces
      // also remove .. that is in the filenames
      $cleanString = preg_replace('/[^A-Za-z0-9._ ]/', ' ', str_replace('..', '', $curLine));

      // remove extra spaces and make string all lower case
      $cleanString = strtolower(preg_replace('/\s+/', ' ', $cleanString));

      // remove duplicate keywords in the srchindex
      $srchArr = explode(' ', $cleanString);

      // remove any items less than 2 characters
      // as fulltext searches need at least 2 characters
      $counter = 0;
      foreach ($srchArr as $value) {
        if (strlen($value)<2) {
          unset($srchArr[ $counter ]);
        }
       $counter++;
      }

      // remove duplicate keywords from the srchIndex
      $srchArr = array_unique($srchArr);
      return(implode(' ', $this->removeCommonWords($srchArr)));
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
    public function mergeLines($tkns1, $tkns2) {
        $numItem = count($tkns1) - 1;
        $tkns1[ $numItem ] = $tkns1[ $numItem ] . ' ' . $tkns2[ 0 ];
        unset($tkns2[ 0 ]);
        return( (count($tkns2) > 0) ? array_merge($tkns1, $tkns2) : $tkns1 );
    }

     /**
     * Takes string and converts spaces to _, removes special characters,
     * and converts string to lower case characters.
     * @param string $string
     * @return string
     */      
    function formatFieldName($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with underscores.

        $string = preg_replace('/[^A-Za-z0-9\-_]/', '', $string); // Removes special chars.

        return strtolower($string);
    }      
}
