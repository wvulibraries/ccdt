<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Adapters;

use Illuminate\Support\Facades\DB;
use App\Models\StopWords;
use App\Models\Table;

use Log;

class OptimizeSearchAdapter {
    private $items;
    private $tblNme;

    /**
     * loop over search array and remove any words that
     * are in the StopWords table.
     * @param       array $search Input array
     * @return      array of strings
     */    
    public function removeCommonWords() {
      foreach($this->items as $key => $word) {
        if (StopWords::isStopWord(strtolower($word))) {
          unset($this->items[$key]);
        }
      }
    }    

    /**
     * optimizes $this->items to be used in full text search
     */
    public function optimizeSrchIndex() {
        // remove duplicate keywords from the srchIndex
        $this->items = array_unique($this->items);

        // remove any items less than 2 characters
        // as fulltext searches need at least 2 characters
        $counter = 0;
        foreach ($this->items as $value) {
            if (strlen($value)<2) {
                unset($this->items[ $counter ]);
            }
            $counter++;
        }

        // remove common words from search index
        $this->removeCommonWords(); 

        return (implode(" ", $this->items));
    }    

    public function process($tblNme) {
      $this->tblNme = $tblNme;
      \DB::table($this->tblNme)->orderBy('id')->chunk(100, function ($records) {
        foreach ($records as $record) {
          // remove extra characters replacing them with spaces
          // also remove .. that is in the filenames
          // remove . seen in prefix on names like Mr. Ms.
          $cleanString = preg_replace('/[^A-Za-z0-9._ ]/', ' ', str_replace('. ', ' ', str_replace('..', '', $record->srchindex)));

          // Convert current search index into an array
          $this->items = explode(' ', (trim(preg_replace('/\s+/', ' ', $cleanString))));

          // echo '<pre>' , var_dump($this->items) , '</pre>';    
          // var_dump($this->optimizeSrchIndex());            
          // die();
          
          \DB::table($this->tblNme)
                  ->where('id', $record->id)
                  ->update(['srchindex' => $this->optimizeSrchIndex()]);
        }
    });

    }

}