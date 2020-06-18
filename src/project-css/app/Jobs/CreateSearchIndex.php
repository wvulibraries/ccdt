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

    private $tblNme;

    /**
     * Create a new job instance.
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
        try{
            $adapter = new SearchIndexAdapter;
            // Build search index on all records in table
            $adapter->process($this->tblNme);
        }catch(\Exception $e){
            Log::error($e->getMessage());
        }          
    }

}