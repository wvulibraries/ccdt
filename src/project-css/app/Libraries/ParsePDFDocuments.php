<?php

namespace App\Libraries;

use Spatie\PdfToImage\Pdf;

class ParsePDFDocuments {
    /**
     * Parse PDF Documents
     *
     * These functions are to help with pulling out the text
     * that is in either doc or docx files.
     *
     */

   /**
    * checks if files exists in storage under the folder
    * for the table
    *
    * @param       string $filename Input string
    *              this should containe file path and filename
    * @return
    */
    function parsePDF($filename)
    {
        if(!$filename || !file_exists($filename)) return false;

        // try basic package to convert PDF file to Text
        $fileContents = (\Spatie\PdfToText\Pdf::getText($filename));
        // if we get no results try to ocr the file
        if (strlen($fileContents) == 0) {
          // try to convert pdf to a png then we will try to
          // ocr the image instead using tesseract ocr
          $pdf = new Pdf($filename);

          // random number is used for the temporary files
          $randomNum = mt_rand();
          $pngPath = 'app/tmp/' . $randomNum . '.png';

          // convert pdf to png file then save
          $pdf->saveImage(storage_path($pngPath));
          $source = storage_path($pngPath);

          $outPath = 'app/tmp/';
          $destination = storage_path($outPath . $randomNum);

          // call tesseract to convert the png to text file
          exec("tesseract $source $destination");
          // read file into $fileContents
          $fileContents = file_get_contents($destination . '.txt');

          // cleanup temporary files
          unlink($source);
          unlink($destination . '.txt');
        }
        return($fileContents);
    }

}
