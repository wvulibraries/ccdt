<?php

namespace App\Libraries;

class CustomStringHelper {

    // checks if files exists in storage under the folder for the table
    public function fileExists($tblNme, $str) {
      return \Storage::exists($tblNme . '/' . $str);
    }

    public function fileExistsInFolder($tblNme, $str) {
      return \Storage::exists($tblNme . '/' . $this->getFolderName($str) . '/' . $this->getFilename($str));
    }

    public function separateFiles($str) {
      $filesArray = explode('^',$str);
      // if (count($filesArray) > 0) {
      //   for ($arrayPos = 0; $arrayPos < count($filesArray); $arrayPos++) {
      //     $filesArray[$arrayPos] = $this->getFilename($filesArray[$arrayPos]);
      //   }
      // }
      return $filesArray;
    }

    // takes a string with a windows style path and returns only the filename
    public function getFilename($str) {
      // explode string
      $tokens = explode('\\',$str);
      // get filename from end of string
      $filename = end($tokens);
      // return filename
      return $filename;
    }

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

    public function cleanSearchString($search) {
      //replace ? with * for wildcard searches
      $str = str_replace('?', '*', $search);
      $str = trim($str);
      $str = htmlspecialchars($str);

      //return string as lowercase
      return strtolower($str);
    }

}
