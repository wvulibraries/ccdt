<?php

namespace App\Libraries;

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
      return \Storage::exists($tblNme . '/' . $str);
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
      return \Storage::exists($tblNme . '/' . $this->getFolderName($str) . '/' . $this->getFilename($str));
    }

    /**
     * Separates the string into an array of flatfiles
     * by using explode and the ^ as a separator
     * @param       string  $str    Input string
     * @return      array of strings
     */
    public function separateFiles($str) {
      $filesArray = explode('^',$str);
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
      $tokens = explode('\\',$str);
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
      $tokens = explode('\\',$str);
      // get filename from end of string
      $filename = end($tokens);
      // get the last folder the file exists in
      $subfolder = prev($tokens);
      //return folder name
      return $subfolder;
    }

    /**
     * Tries to clean search string of extra spaces
     * also uses htmlspecialchars to safeguard AGAINST
     * sql injection. Also replaces ? that is sometimes
     * used as a wildcard and replaces it with a *
     * @param       string  $search    Input string
     * @return      string
     */
    public function cleanSearchString($search) {
      //replace ? with * for wildcard searches
      $str = str_replace('?', '*', $search);
      $str = trim($str);
      $str = htmlspecialchars($str);

      //return string as lowercase
      return strtolower($str);
    }

    public function checkForSSN($fileContents)
    {
        // if we have pulled the text from the file next we need to scan for
        // any social security numbers using regex pattern
        if ($contents != null) {
            // finalise the regular expression, matching the whole line
            $pattern = '#\b[0-9]{3}-[0-9]{2}-[0-9]{4}\b#';

            // preg_match_all will return a count if it is greater than
            // 0 we have matches against the SSN pattern and will return
            // a true value
            if(preg_match_all($pattern, $contents, $matches) > 0){
                return(true);
            }

        }
        return(false);
    }

}
