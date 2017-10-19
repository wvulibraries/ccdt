<?php

namespace App\Libraries;

class TikaConvert {
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
    function convert($filename)
    {
        // verify that file exists
        if(!$filename || !file_exists($filename)) return false;

        // verify that tika server is accepting connections
        if(!$this->serverOpen()) return false;

        // this is one way to use the tika server using the exec command creates a temporary file
        # $randomNum = mt_rand();
        # $pngPath = 'app/tmp/' . $randomNum . '.txt';
        # $destination = storage_path($pngPath);
        # exec("curl -T " . $filename . " http://tika:9998/tika > " . $destination);
        # $fileContents = file_get_contents($destination);
        # unlink($destination);

        // this method doesn't use exec or temp files that need deleted
        // Set where to connect to
        $ch = curl_init("http://" . $TIKA_HOST . ':' . $TIKA_PORT . "/tika");
        // Request will be a PUT
        curl_setopt($ch, CURLOPT_PUT, 1);
        $fh_res = fopen($filename, 'r');

        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filename));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Send the request
        $fileContents = curl_exec ($ch);
        fclose($fh_res);

        return($fileContents);
    }

    function serverOpen() {
      $connection = @fsockopen($TIKA_HOST, $TIKA_PORT);

      if (is_resource($connection))
      {
          fclose($connection);
          return(true);
      }
      else
      {
          return(false);
      }
    }

}
