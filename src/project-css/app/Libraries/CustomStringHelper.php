<?php

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

    public function removeCommonWords($search) {
      // loop over search array and remove any words that
      // are in the StopWords table.
      foreach($search as $key => $word) {
        if (StopWords::isStopWord($word)) {
          unset($search[$key]);
        }
      }
      return $search;
    }

    /**
     * @param string $fileContents
     */
    public function ssnExists($fileContents) {
        // ssnExists uses preg_match_all to detect a vaild social security
        // number pattern. If the number of matches are above 0 then we
        // will return true.
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

}
