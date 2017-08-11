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
      $tokens = explode('\\',$str);
      $filename = end($tokens);
      return $filename;
    }

    public function getFolderName($str) {
      $tokens = explode('\\',$str);
      $filename = end($tokens);
      $subfolder = prev($tokens);
      //build new string containing folder
      return $subfolder;
    }

    public function cleanSearchString($search) {
      //replace ? with * for wildcard searches
      $str = str_replace('?', '*', $search);

      return $str;
    }

}
