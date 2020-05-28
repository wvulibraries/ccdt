<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\StopWords;
use App\Models\Table;
use Log;

class CreateSearchIndex implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tblNme;
    private $items;

    /**
     * Create a new job instance.
     */
    public function __construct($tblNme)
    {
        $this->tblNme = $tblNme;
    }

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
     * takes a string and prepares it to be used as a search index for fulltext search
     * @param string $curLine
     * @return string
     */
    public function createSrchIndex() {
        // remove unused fields for search
        unset ($this->items["id"], $this->items["srchindex"], $this->items["created_at"], $this->items["updated_at"]);

        // remove any items less than 2 characters
        // as fulltext searches need at least 2 characters
        $counter = 0;
        foreach ($this->items as $value) {
            if (strlen($value)<2) {
                unset($this->items[ $counter ]);
            }
            $counter++;
        }

        // remove duplicate keywords from the srchIndex
        $this->items = array_unique($this->items);

        $this->removeCommonWords(); 

        // remove extra characters replacing them with spaces
        // also remove .. that is in the filenames
        $cleanString = preg_replace('/[^A-Za-z0-9._ ]/', ' ', str_replace('..', '', implode(" ", $this->items)));

        // remove extra spaces and make string all lower case
        return (strtolower(preg_replace('/\s+/', ' ', $cleanString)));
    }    

    /**
     * Execute the job.
     */
    public function handle()
    {
        try{
            // insure table import has started and records exist
            while (\DB::table($this->tblNme)->count() == 0) {
              //sleep for 3 seconds
              sleep(3);
            }

            \DB::table($this->tblNme)->orderBy('id')->chunk(100, function ($records) {
                foreach ($records as $record) {
                    $this->items = (array) $record;                   

                    \DB::table($this->tblNme)
                            ->where('id', $record->id)
                            ->update(['srchindex' => $this->createSrchIndex()]);
                }
            });
        }catch(\Exception $e){
            Log::error($e->getMessage());
        }          
    }

}
