<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\OptimizeSearch;

use Log;

/**
 * Optimize Search Index dispatches OptimizeSearch job(s)
 * each job updates at most 500 records at a time. Jobs may 
 * run concurrently depending on how many queue workers are
 * currently configured. Currently this is setup for the low 
 * queue.
 * 
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class OptimizeSearchIndex implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tblNme;  

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
     * The handle function contains code to be executed for 
     * the job. 
     * 
     * handle first gets the record count of the table and dispatches OptimizeSearch jobs.
     * lookups Completed are the number of jobs we have dispatched. Once no more records
     * remain the job ends.
     * 
     * @return void
     */
    public function handle()
    {
        $recordCount = DB::table($this->tblNme)->count();
        $recordsRemaining = true;
        $lookupsCompleted = 0;
        $chunkSize = 500;         

        while($recordsRemaining){
            dispatch(new OptimizeSearch($this->tblNme, $chunkSize*$lookupsCompleted, $chunkSize))->onQueue('low');              

            if($recordCount < $chunkSize + ($chunkSize*$lookupsCompleted)){
                $recordsRemaining = false;
            }

            $lookupsCompleted++;
        }         
    }    

}