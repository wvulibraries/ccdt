<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Jobs;

use App\Adapters\SearchIndexAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class CreateSearchIndex implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    public $tblNme;  

    /**
     * Create a new job instance.
     *
     * @param string $tblNme
     *  
     */
    public function __construct($tblNme)
    {
        $this->tblNme = $tblNme;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $adapter = new SearchIndexAdapter;
        // Build search index on all records in table
        $adapter->process($this->tblNme);         
    }   

}