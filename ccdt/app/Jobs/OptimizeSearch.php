<?php

namespace App\Jobs;

use App\Adapters\UpdateSearchAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

use Log;

/**
 * Optimize Search Job calls the Update Search
 * adapter cleans the search string by removing 
 * invalid characters, removes common words and 
 * single characters from the search index.
 * 
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class OptimizeSearch implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tblNme;
    private $skipCount;
    private $chunkSize;
    private $records;  

    /**
     * Create a new job instance.
     * 
     * @param string $tblNme (name of the table)
     * @param integer $skipCount (number of records to skip)
     * @param integer $chunkSize (number of records to be read)
     *  
     */
    public function __construct($tblNme, $skipCount, $chunkSize)
    {
        $this->tblNme = $tblNme;
        $this->skipCount = $skipCount;
        $this->chunkSize = $chunkSize;
    } 

    /**
     * The handle function contains code to be executed for 
     * the job. 
     * 
     * @return void
     */
    public function handle()
    {
        $this->records = DB::table($this->tblNme)->skip($this->skipCount)->take($this->chunkSize)->get();

        $adapter = new UpdateSearchAdapter;

        foreach ($this->records as $record) {
            // Optimize Search index on all records in table
            $adapter->process($this->tblNme, $record->id, $record->srchindex);
        }         
    }   

}