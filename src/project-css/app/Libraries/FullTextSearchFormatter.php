<?php

namespace App\Libraries;
use App\Libraries\CustomStringHelper;

class FullTextSearchFormatter {
// class contains functions that will check and properly Format
// a string that can be used for full text boolean searches
// with sql injection in mind.

    private function sanitizeSearchTerm($searchPhrase) {
       //replace ? with * for wildcard searches
      $searchPhrase = str_replace('?', '*', $searchPhrase);
      $searchPhrase = str_replace("+", " +", $searchPhrase);
      $searchPhrase = str_replace("-", " -", $searchPhrase);
      $searchPhrase = str_replace("@", "", $searchPhrase);
      $searchPhrase = str_replace("  ", " ", $searchPhrase);
      return $searchPhrase;
    }

    // used by array_filter below filters out items less than 2 characters
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
      $searchTerms = strip_tags(trim($search));
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

    public function hasRelevancyModifier($word) {
      if (!empty($word)) {
        $count = substr_count('<>', $word[0]);
        if ($count == 1) {
          return true;
        }
      }
      return false;
    }

    public function hasExactTerm($string) {
      $count = substr_count($string, '"');
      if ($count == 2) {
        return true;
      }
      return false;
    }

    public function hasMatchEither($string) {
      $startCount = substr_count($string, '(');
      $endCount = substr_count($string, ')');
      if (($startCount == 1) && ($endCount == 1)) {
        return (strpos($string, '(') < strpos($string, ')'));
      }
      return false;
    }

    public function getMatchEither($string) {
      // returns string between ()
      $startPos = strpos($string, '(') + 1;
      $endPos = strpos($string, ')') - $startPos;
      return substr($string, $startPos, $endPos);
    }

    public function cleanMatchEither($string) {
      if ($this->hasMatchEither($string)) {
        $eitherString = $this->getMatchEither($string);
        $eitherArray = (explode(' ', $eitherString));
        foreach ($eitherArray as &$value) {
          if ($this->hasRelevancyModifier($value)) {
            $modifier = $value[0];
            $value = substr($value, 1, strlen($value) - 1);
            $value = $modifier . preg_replace('/[^A-Za-z0-9]/', '', $value);
          }
          else {
            $value = preg_replace('/[^A-Za-z0-9]/', '', $value);
          }
        }
        return '+(' . implode(' ', $eitherArray) .')';
      }
      return false;
    }

    public function prepareSearch($search) {
      // if the string is wrapped in quotes we add them back after the preg_replace
      // a exact term search is wrapped like "example search"
      if ($this->hasExactTerm($search)) {
        $searchTerms = $this->cleanSearchString('/[^A-Za-z0-9 ]/', $search);

        // add back the quotes
        $searchTerms = '"' . $searchTerms . '"';
        var_dump($searchTerms);
        return $searchTerms;
      }
      else if ($this->hasMatchEither($search)) {
        // remove either section of string
        $main = $this->cleanSearchString('/[^a-zA-Z+-~ ]/', substr($search, 0, strpos($search, '(')));
        //cleans and properly format the Match either part of the search string
        $matchEither = $this->cleanMatchEither($search);
        $searchTerms = $main . ' ' . $matchEither;
        var_dump($searchTerms);
        return $searchTerms;
      }
      // if string isn't a exact term search or match either search
      $searchTerms = $this->cleanSearchString('/[^a-zA-Z+-~ ]/', $search);
      var_dump($searchTerms);
      // die();
      return $searchTerms;
    }
}
