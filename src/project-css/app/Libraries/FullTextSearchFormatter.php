<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Libraries;
use App\Helpers\CustomStringHelper;

/**
 * Full Text Search Formatter
 * 
 * class contains functions that will check and properly Format
 * a string that can be used for full text boolean searches
 * with sql injection in mind.
 */
class FullTextSearchFormatter {

     /**
     * sanitizes and corrects search string for full text search
     * 
     * @param       string  $searchPhrase    Input string
     * @return      string
     */
    private function sanitizeSearchTerm($searchPhrase) {
       //replace ? with * for wildcard searches
      $searchPhrase = str_replace('?', '*', $searchPhrase);
      $searchPhrase = str_replace("+", " +", $searchPhrase);
      $searchPhrase = str_replace("-", " -", $searchPhrase);
      $searchPhrase = str_replace("@", "", $searchPhrase);
      $searchPhrase = str_replace("  ", " ", $searchPhrase);
      return $searchPhrase;
    }

     /**
     * returns true if string is greater than 1 character
     * 
     * @param       string  $string    Input string
     * @return      string
     */
    public function testLength($string) {
      return (strlen($string) > 1);
    }

    /**
     * Tries to clean search string of extra spaces also uses strip_tags
     * to safeguard AGAINST sql injection. Also replaces ? that is
     * sometimes used as a wildcard and replaces it with a *
     * @param       string  $search    Input string
     * @return      string
     */
    public function cleanSearchString($pattern, $search) {
      // remove excess white space
      $searchTerms = strip_tags($search);
      // remove all chars except for letters, numbers and space
      $value = preg_replace($pattern, '', $searchTerms);
      $searchTerms = $this->sanitizeSearchTerm($value);
      $searchArray = (explode(' ', $searchTerms));

      // check remove items with 0 length
      $searchArray = array_values(array_filter(array_map('trim', $searchArray), array($this, 'testLength')));

      // leave only unique items in array
      $searchArray = array_unique($searchArray);

      $str = implode(' ', (new customStringHelper)->removeCommonWords($searchArray));
      return strtolower($str);
    }

    /**
     * check to see if a relevancy modifier 
     * exists in the string
     *
     * @param string $word
     * @return boolean
     */   
    public function hasRelevancyModifier($word) : bool {
      $count = substr_count('<>', $word[0]);
      return ($count == 1);
    }

    /**
     * verify that string is quoted "some string"
     * that is used for a exact search
     *
     * @param string $string
     * @return boolean
     */       
    public function hasExactTerm($string) : bool {
      $count = substr_count($string, '"');
      return ($count == 2);
    }

    /**
     * checks to see if ( is before ) 
     *
     * @param string $string the string should contain ()
     * @return boolean
     */    
    public function hasMatchEither($string) : bool {
      // verifes that the match either grouping is correct
      return (strpos($string, '(') < strpos($string, ')'));
    }

    /**
     * checks to see is string contains a wildcard at the end
     *
     * @param string $string
     * @return boolean
     */  
    public function hasWildCard($string) : bool {
      return (mb_substr($string, -1) == '*');
    }

    /**
     * returns string between ()
     *
     * @param string $string the string should contain ()
     * @return mixed substr or false if substring cannot be returned
     */    
    public function getMatchEither($string) {
      $startPos = strpos($string, '(') + 1;
      $endPos = strpos($string, ')') - $startPos;
      if ($startPos < $endPos) {
        return substr($string, $startPos, $endPos);
      }
      return false;
    }

    /**
     * cleans string to insure it is correctly formatted
     * used for mysql advanced text searching using full-text queries
     *
     * @param string $string
     * @return string $value
     */    
    public function cleanArrayItem($string) {
      if ($this->hasRelevancyModifier($string)) {
        $value = $string[0] . preg_replace('/[^A-Za-z0-9]/', '', substr($string, 1, strlen($string) - 1));
      }
      else {
        $value = preg_replace('/[^A-Za-z0-9]/', '', $string);
      }

      // add back wildcard at to the string if it was present before
      if ($this->hasWildCard($string)) {
        $value = $value . '*';
      }

      return $value;
    }

    /**
     * cleans string for match either items
     * ie +nice +(language country)
     *
     * @param string $string
     * @return string
     */        
    public function cleanMatchEither($string) {
      // words in match either query
      $eitherString = $this->getMatchEither($string);
      // separate section into a array
      $eitherArray = (explode(' ', $eitherString));
      foreach ($eitherArray as &$value) {
        // clean items in array
        $value = $this->cleanArrayItem($value);
      }
      // combine and return properly formmated string
      return '+(' . implode(' ', $eitherArray) .')';
    }

    /**
     * properly format search string
     *
     * @param string $search
     * @return string
     */      
    public function prepareSearch($search) {
      // if the string is wrapped in quotes we add them back after the preg_replace
      // a exact term search is wrapped like "example search"
      if ($this->hasExactTerm($search)) {
        // clean string and then add back the quotes
        $searchTerms = '"' . $this->cleanSearchString('/[^A-Za-z0-9 ]/', $search) . '"';
      }
      else if ($this->hasMatchEither($search)) {
        // remove either section of string
        $main = $this->cleanSearchString('/[^a-zA-Z+-~ ]/', substr($search, 0, strpos($search, '(')));
        //cleans and properly format the Match either part of the search string then combine both parts
        $searchTerms = $main . ' ' . $this->cleanMatchEither($search);
      }
      else {
        // if string isn't a exact term search or match either search
        $searchTerms = $this->cleanSearchString('/[^a-zA-Z+-~* ]/', $search);
      }
      return $searchTerms;
    }
}
