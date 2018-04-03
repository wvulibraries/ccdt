<?php

namespace App\Libraries;

use Illuminate\Support\Facades\DB;

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
        return \Storage::exists($tblNme.'/'.$str);
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
        return \Storage::exists($tblNme.'/'.$this->getFolderName($str).'/'.$this->getFilename($str));
    }

    /**
     * Separates the string into an array of flatfiles
     * by using explode and the ^ as a separator
     * @param       string  $str    Input string
     * @return      array of strings
     */
    public function separateFiles($str) {
        $filesArray = explode('^', $str);
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
        $tokens = explode('\\', $str);
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
        $tokens = explode('\\', $str);
        // get filename from end of string
        $filename = end($tokens);
        // get the last folder the file exists in
        $subfolder = prev($tokens);
        return $subfolder;
    }

    private function sanitizeSearchTerm($searchPhrase) {
       //replace ? with * for wildcard searches
      $searchPhrase = str_replace('?', '*', $searchPhrase);
      $searchPhrase = str_replace("+", " +", $searchPhrase);
      $searchPhrase = str_replace("-", " -", $searchPhrase);

      return $searchPhrase;
    }

    // used by array_filter below filters out items less than 2 characters
    public function testLength($string) {
      return (strlen($string) > 1);
    }

    public function removeCommonWords($search) {
      $query = [];
      //$commonwords = 'a,an,and,I,it,is,do,does,for,from,go,how,the,etc';
      //$commonwords = explode(",", $commonwords);
      $StopWords=array("a","able","about","across","after","all","almost","also","am","among","an","and","any","are","as","at","be","because","been","but","by","can","cannot","could","dear","did","do","does","either","else","ever","every","for","from","get","got","had","has","have","he","her","hers","him","his","how","however","i","if","in","into","is","it","its","just","least","let","like","likely","may","me","might","most","must","my","neither","no","nor","not","of","off","often","on","only","or","other","our","own","rather","said","say","says","she","should","since","so","some","than","that","the","their","them","then","there","these","they","this","tis","to","too","twas","us","wants","was","we","were","what","when","where","which","while","who","whom","why","will","with","would","yet","you","your","ain't","aren't","can't","could've","couldn't","didn't","doesn't","don't","hasn't","he'd","he'll","he's","how'd","how'll","how's","i'd","i'll","i'm","i've","isn't","it's","might've","mightn't","must've","mustn't","shan't","she'd","she'll","she's","should've","shouldn't","that'll","that's","there's","they'd","they'll","they're","they've","wasn't","we'd","we'll","we're","weren't","what'd","what's","when'd","when'll","when's","where'd","where'll","where's","who'd","who'll","who's","why'd","why'll","why's","won't","would've","wouldn't","you'd","you'll","you're","you've");
      foreach($search as $value){
        if(!in_array($value, $StopWords)){
            array_push($query, $value);
        }
      }
      return $query;
    }

    public function searchFormatter($searchterm) {
        $searchTerms = preg_replace('/[^A-Za-z0-9-+<>"()*._ ]/', ' ', $searchterm);
        $searchTerms = $this->sanitizeSearchTerm($searchTerms);
        $searchArray = (explode(' ', $searchTerms));

        // check remove items with 0 length
        $searchArray = array_values(array_filter(array_map('trim', $searchArray), array($this, 'testLength')));
        // leave only unique items in array
        $searchArray = array_unique($searchArray);

        $searchterm = (implode(' ', $this->removeCommonWords($searchArray)));

        return $searchterm;
    }

    /**
     * Tries to clean search string of extra spaces also uses strip_tags
     * to safeguard AGAINST sql injection. Also replaces ? that is
     * sometimes used as a wildcard and replaces it with a *
     * @param       string  $search    Input string
     * @return      string
     */
    public function cleanSearchString($str) {
       $str = strip_tags(trim($str));
       $str = $this->searchFormatter($str);
       // echo $str;
       // die();
       return strtolower($str);
    }

    /**
     * @param string $fileContents
     */
    public function ssnExists($fileContents) {
        // ssnExists uses preg_match_all to detect a vaild social security
        // number pattern. If the number of matches are above 0 then we
        // will return true.
        if ($fileContents != null) {
            // regex patter we will use to detect a social security number
            $pattern = '#\b[0-9]{3}-[0-9]{2}-[0-9]{4}\b#';

            // preg_match_all will return a count if it is greater than
            // 0 we have matches against the SSN pattern and will return
            // a true value
            if (preg_match_all($pattern, $fileContents, $matches)>0) {
                return(true);
            }

        }
        return(false);
    }

    /**
     * If ssnExists returns true we use a preg_replace
     * to replace the social with ###-##-####
     * @param       string  $fileContents    Input string
     * @return      string
     */
    public function ssnRedact($fileContents) {
        $pattern = '#\b[0-9]{3}-[0-9]{2}-[0-9]{4}\b#';
        $redacted = '###-##-####';
        if ($this->ssnExists($fileContents)) {
            return (preg_replace($pattern, $redacted, $fileContents));
        }
        return($fileContents);
    }

    /*
    * @param string $string
    * @return array with filenames
    */
    public function checkForFilenames($string) {
      $fileExtensions = array("txt", "doc", "docx", "pdf", "xls", "xlsx", "ppt", "pptx", "jpg");
      $foundFiles = [];
      $pieces = explode("/", $string);
      foreach ($fileExtensions as $extension) {
        foreach ($pieces as $value) {
          if (strpos($value, '.'.$extension) !== false) {
            array_push($foundFiles, $value);
          }
        }
      }
      return ($foundFiles);
    }

}
