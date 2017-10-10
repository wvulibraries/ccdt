<?php

namespace App\Libraries;

class ParseWordDocuments {
    /**
     * Parse Word Documents
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
    function parseDoc($filename)
    {
        if(!$filename || !file_exists($filename)) return false;

        $fileHandle = fopen($filename, "r");
        $line = @fread($fileHandle, filesize($filename));
        $lines = explode(chr(0x0D),$line);
        $outtext = "";
        foreach($lines as $thisline)
          {
            $pos = strpos($thisline, chr(0x00));
            if (($pos !== FALSE)||(strlen($thisline)==0))
              {
              } else {
                $outtext .= $thisline." ";
              }
          }
        return $outtext;
    }

    function parseDocx($filename){

        if(!$filename || !file_exists($filename)) return false;

        $striped_content = '';
        $content = '';

        $zip = zip_open($filename);
        if (!$zip || is_numeric($zip)) return false;

        while ($zip_entry = zip_read($zip)) {

            if (zip_entry_open($zip, $zip_entry) == FALSE) continue;

            if (zip_entry_name($zip_entry) != "word/document.xml") continue;

            $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

            zip_entry_close($zip_entry);
        }
        zip_close($zip);
        $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $striped_content = strip_tags($content);

        return $striped_content;
    }

}
