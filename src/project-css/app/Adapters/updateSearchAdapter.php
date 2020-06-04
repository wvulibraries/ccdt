<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Adapters;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\UpdateSearchIndex;

use App\Models\StopWords;

use Log;

class UpdateSearchAdapter {
    private $items;

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

    public function process($tblNme, $id, $srchIndex) {
        // remove extra characters replacing them with spaces
        // also remove .. that is in the filenames
        // remove . seen in prefix on names like Mr. Ms.
        $cleanString = preg_replace('/[^A-Za-z0-9._ ]/', ' ', str_replace('. ', ' ', str_replace('..', '', $srchIndex)));

        // Convert current search index into an array
        $this->items = explode(' ', (trim(preg_replace('/\s+/', ' ', $cleanString))));
  
        $index = $this->optimizeSrchIndex();
        // echo '<pre>' , var_dump($index) , '</pre>';  
        // die();
        
        DB::table($tblNme)
                ->where('id', $id)
                ->update(['srchindex' => $index]);        
    }

}