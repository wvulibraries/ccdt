<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Adapters;

class TikaConvert {
    /**
     * TikaConvert helps connect to the tika server to perform various
     * file type conversions and ocr'ing of images
     */
    private $tika_host;
    private $tika_port;

    function __construct() {
        $this->tika_host = env('TIKA_HOST', 'localhost');
        $this->tika_port = env('TIKA_PORT', '9998');
    }

     /**
     * Sets the port of the tika host
     * @param string $host
     */     
    function setTikaHost($host) {
      // to do add validation of $host data
      // ensure it is a vaild ip address or hostname number
      // only numeric
      $this->tika_host = $host;
    }

     /**
     * Sets the port of the tika host
     * @param string $host
     */       
    function setTikaPort($port) {
      // to do add validation of $host data
      // ensure it is a vaild port number
      // only numeric
      $this->tika_port = $port;
    }

    /**
     *
     * @param       string $filename Input string
     *              this should containe file path and filename
     * @return      string detected text from source file
     */
    function convert($filename)
    {
        // verify that file exists
        if (!$filename || !file_exists($filename)) {
          return false;
        }

        // verify that tika server is accepting connections
        if (!$this->serverOpen()) {
          return false;
        }

        // this is one way to use the tika server using the exec command creates a temporary file
        # $randomNum = mt_rand();
        # $pngPath = 'app/tmp/' . $randomNum . '.txt';
        # $destination = storage_path($pngPath);
        # exec("curl -T " . $filename . " http://" . $this->tika_host . ":" . $this->tika_port. "/tika > " . $destination);
        # $fileContents = file_get_contents($destination);
        # unlink($destination);

        // this method doesn't use exec or temp files that need deleted
        // Set where to connect to
        $ch = curl_init("http://".$this->tika_host.':'.$this->tika_port."/tika");
        // Request will be a PUT
        curl_setopt($ch, CURLOPT_PUT, 1);
        $fh_res = fopen($filename, 'r');

        curl_setopt($ch, CURLOPT_INFILE, $fh_res);
        curl_setopt($ch, CURLOPT_INFILESIZE, filesize($filename));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // Send the request
        $fileContents = curl_exec($ch);
        fclose($fh_res);

        return($fileContents);
    }

    /**
     * Tries to open a socket for the set tika server
     * If succssful returns true otherwise returns false
     * @return      boolean
     */    
    function serverOpen() {
        $connection = @fsockopen($this->tika_host, $this->tika_port);

        if (is_resource($connection)) {
            fclose($connection);
            return(true);
        }
        return(false);
    }

}
