<?php

namespace App\Libraries;

class Helper {

    // checks if files exists in storage under the folder for the table
    public function fileExists($tblNme, $filename) {
      // if \ exist in the filename call getFilename
      if (strpos($filename, '\\') !== FALSE) {
        // call getFilename to strip off the old path to the file
        $filename = $this->getFilename($filename);
      }

      return \Storage::exists($tblNme . '/' . $filename);
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
      return end($tokens);
    }
}
