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

}
