<?php

namespace App\Libraries;

use App\Libraries\tikaConvert;
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

          // set path for temporary file storage
          $tmpPath = 'app/tmp/';

          // random number is used for the temporary files
          $randomNum = mt_rand();
          $pngLocation = storage_path($tmpPath . $randomNum . '.png');

          // read pdf and save as a png file
          $pdf = new Pdf($filename);

          for ($page = 1; $page <= $pdf->getNumberOfPages(); $page++) {
              //$page = 1;
              $pdf->setPage($page)->saveImage($pngLocation);

              //$txtLocation = storage_path($tmpPath . $randomNum . '_' . $page . '.txt');
              // call tika to convert the png to text file
              //exec("curl -T " . $pngLocation . " http://tika:9998/tika > " . $txtLocation);

              //var_dump($pngLocation);
              //die();

              if ($page == 1) {
                $fileContents = (new tikaConvert)->convert($pngLocation);
              }
              else {
                $fileContents = $fileContents . ' ' . (new tikaConvert)->convert($pngLocation);
              }

              // cleanup temporary files
              unlink($pngLocation);
          }

          // read file into $fileContents
          //$fileContents = file_get_contents($txtLocation);
          //unlink($txtLocation);
        }
        return($fileContents);
    }

}
