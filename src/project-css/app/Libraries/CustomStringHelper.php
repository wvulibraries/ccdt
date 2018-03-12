<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

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

    /**
     * checks if files exists in storage under the folder
     * for the table
     *
     * @param       string  $tblNme    Input string
     * @param       string  $str    Input string
     * @return      boolean
     */
    public function fileExists($tblNme, $str) {
        return \Storage::exists($tblNme.'/'.$str);
    }

    /**
     * Determines if the file currently exists in the storage
     * folder under the current table name it uses the last
     * folder and filename in the $str that is passed.
     *
     * @param       string  $tblNme    Input string
     * @param       string  $str    Input string
     * @return      boolean
     */
    public function fileExistsInFolder($tblNme, $str) {
        return \Storage::exists($tblNme.'/'.$this->getFolderName($str).'/'.$this->getFilename($str));
    }

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
     * Takes a string with a windows style path to a file
     * and returns only the filename
     * @param       string  $str    Input string
     * @return      string
     */
    public function getFilename($str) {
        // explode string
        $tokens = explode('\\', $str);
        // get filename from end of string
        $filename = end($tokens);
        // return filename
        return $filename;
    }

    /**
     * Takes a string with a windows style path
     * and returns only the last folder before the
     * filename
     * @param       string  $str    Input string
     * @return      string
     */
    public function getFolderName($str) {
        // explode string
        $tokens = explode('\\', $str);
        // get filename from end of string
        $filename = end($tokens);
        // get the last folder the file exists in
        $subfolder = prev($tokens);
        return $subfolder;
    }

    private function sanitizeSearchTerm($searchPhrase) {
      $searchPhrase = str_replace("@@", " ", $searchPhrase);
      $searchPhrase = str_replace("@", " ", $searchPhrase);
      $searchPhrase = str_replace(",", " ", $searchPhrase);
      $searchPhrase = str_replace(";", " ", $searchPhrase);
      $searchPhrase = str_replace("'", " ", $searchPhrase);
      $searchPhrase = str_replace("--", " -", $searchPhrase);
      $searchPhrase = str_replace("/*", " ", $searchPhrase);
      $searchPhrase = str_replace("*/", " ", $searchPhrase);
      $searchPhrase = str_replace("xp_", " ", $searchPhrase);
      $searchPhrase = str_replace("++", " +", $searchPhrase);
      $searchPhrase = str_replace("=", " ", $searchPhrase);
      return $searchPhrase;
    }

    // used by array_filter below filters out items less than 2 characters
    public function testLength($string) {
      return (strlen($string) > 1);
    }

    public function searchFormatter($searchterm) {
        $searchTerms = $this->sanitizeSearchTerm($searchterm);

        $searchArray = (explode(' ', $searchTerms));

        // check remove items with 0 length
        $searchArray = array_values(array_filter(array_map('trim', $searchArray), array($this, 'testLength')));
        // leave only unique items in array
        $searchArray = array_unique($searchArray);

        $searchterm = (implode(' ', $searchArray));

        return $searchterm;
    }

    /**
     * Tries to clean search string of extra spaces also uses strip_tags and
     * mysql_real_escape_string to safeguard AGAINST sql injection. Also
     * replaces ? that is sometimes used as a wildcard and replaces it with a *
     * @param       string  $search    Input string
     * @return      string
     */
    public function cleanSearchString($str) {
       $str = trim($str);
       //replace ? with * for wildcard searches
       $str = str_replace('?', '*', $str);
       $str = $this->searchFormatter($str);

       // echo $str;
       // die();
       return strtolower($str);
    }

    /**
     * @param string $fileContents
     */
    public function ssnExists($fileContents) {
        // ssnExists uses preg_match_all to detect a vaild social security
        // number pattern. If the number of matches are above 0 then we
        // will return true.
        if ($fileContents != null) {
            // regex patter we will use to detect a social security number
            $pattern = '#\b[0-9]{3}-[0-9]{2}-[0-9]{4}\b#';

            // preg_match_all will return a count if it is greater than
            // 0 we have matches against the SSN pattern and will return
            // a true value
            if (preg_match_all($pattern, $fileContents, $matches)>0) {
                return(true);
            }

        }
        return(false);
    }

    /**
     * If ssnExists returns true we use a preg_replace
     * to replace the social with ###-##-####
     * @param       string  $fileContents    Input string
     * @return      string
     */
    public function ssnRedact($fileContents) {
        $pattern = '#\b[0-9]{3}-[0-9]{2}-[0-9]{4}\b#';
        $redacted = '###-##-####';
        if ($this->ssnExists($fileContents)) {
            return (preg_replace($pattern, $redacted, $fileContents));
        }
        return($fileContents);
    }

    /*
    * @param string $string
    * @return array with filenames
    */
    public function checkForFilenames($string) {
      $fileExtensions = array(
          'txt',
          'doc',
          "docx",
          "pdf",
          "xls",
          "xlsx",
          "ppt",
          "pptx",
          "jpg"
      );
      $foundFiles = [];
      $pieces = explode("/", $string);
      foreach ($fileExtensions as $extension) {
        foreach ($pieces as $value) {
          if (strpos($value, '.'.$extension) !== false) {
            array_push($foundFiles, $value);
          }
        }
      }
      return ($foundFiles);
    }

}
