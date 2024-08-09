<?php

namespace App\Jobs;

use App\Adapters\SearchIndexAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

/**
 * Create Search Index Job calls the search index
 * adapter and builds the basic search index for each
 * record after the table has been imported.
 * 
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class CreateSearchIndex implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tblNme;  

    /**
     * Create a new job instance.
     *
     * @param string $tblNme (name of the table)
     *  
     */
    public function __construct($tblNme)
    {
        $this->tblNme = $tblNme;
    }

    /**
     * The handle function contains code to be executed for 
     * the job. 
     * 
     * Handle calls the Search Index Adapter to generate 
     * a new search index on each record of the table.
     * 
     * @return void
     */
    public function handle()
    {
        // Build search index on all records in table
        (new SearchIndexAdapter)->process($this->tblNme);         
    }   

}