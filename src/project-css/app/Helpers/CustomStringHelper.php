<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\AllowedFileTypes;

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

     // regex patter we will use to detect a social security number
     protected $pattern = '#\b[0-9]{3}-[0-9]{2}-[0-9]{4}\b#';

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
     * loop over search array and remove any words that
     * are in the StopWords array.
     * @param       array $search Input array
     * @return      array of strings
     */    
    public function removeCommonWords($search) {
      // common words that are to be removed from search string
      $stopWords = array("a","able","about","across","after","all","almost","also","am","among","an","and","any","are","as","at","be","because","been","but","by","can","cannot","could","dear","did","do","does","either","else","ever","every","for","from","get","got","had","has","have","he","her","hers","him","his","how","however","i","if","in","into","is","it","its","just","least","let","like","likely","may","me","might","most","must","my","neither","no","nor","not","of","off","often","on","only","or","other","our","own","rather","said","say","says","she","should","since","so","some","than","that","the","their","them","then","there","these","they","this","tis","to","too","twas","us","wants","was","we","were","what","when","where","which","while","who","whom","why","will","with","would","yet","you","your","ain't","aren't","can't","could've","couldn't","didn't","doesn't","don't","hasn't","he'd","he'll","he's","how'd","how'll","how's","i'd","i'll","i'm","i've","isn't","it's","might've","mightn't","must've","mustn't","shan't","she'd","she'll","she's","should've","shouldn't","that'll","that's","there's","they'd","they'll","they're","they've","wasn't","we'd","we'll","we're","weren't","what'd","what's","when'd","when'll","when's","where'd","where'll","where's","who'd","who'll","who's","why'd","why'll","why's","won't","would've","wouldn't","you'd","you'll","you're","you've");

      // Remove duplicate Keywords, common words and anything less than 2 characters
      $matchWords = array_filter($search , function ($item) use ($stopWords) { return !($item == '' || in_array($item, $stopWords));});

      return $matchWords;
    }

    /**
     * Detects a social security number pattern in string.
     * 
     * @param string $fileContents
     * @return boolean
     */        
    public function ssnExists($fileContents) {
        if ($fileContents != null) {
            // preg_match_all will return a count if it is greater than
            // 0 we have matches against the SSN pattern and will return
            // a true value
            return (preg_match_all($this->pattern, $fileContents, $matches)>0);
        }
        return (false);
    }

    /**
     * If ssnExists returns true we use a preg_replace
     * to replace the social with ###-##-####
     * @param       string  $fileContents    Input string
     * @return      string
     */
    public function ssnRedact($fileContents) {
        if ($this->ssnExists($fileContents)) {
            return (preg_replace($this->pattern, '###-##-####', $fileContents));
        }
        return($fileContents);
    }

    /*
    * @param string $string
    * @return array with filenames
    */
    public function checkForFilenames($string) {
      $foundFiles = [];
      $pieces = explode("/", $string);
      foreach ($pieces as $value) {
        $ext = substr($value, strrpos($value,'.')+1);
        if (AllowedFileTypes::isAllowedType($ext)) {
          array_push($foundFiles, $value);
        }
      }
      return ($foundFiles);
    }

     /**
     * Takes string and converts spaces to _, removes special characters,
     * and converts string to lower case characters.
     * @param string $string
     * @return string
     */      
    function formatFieldName($string) {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with underscores.

        $string = preg_replace('/[^A-Za-z0-9\-_]/', '', $string); // Removes special chars.

        return strtolower($string);
    }      
}
