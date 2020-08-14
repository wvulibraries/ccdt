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

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\OptimizeSearch;

use Log;

class OptimizeSearchIndex implements ShouldQueue
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