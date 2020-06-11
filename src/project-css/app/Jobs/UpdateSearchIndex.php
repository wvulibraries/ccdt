<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Jobs;

use App\Adapters\UpdateSearchAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

use Log;

class UpdateSearchIndex implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tblNme;
    private $records;

    /**
     * Create a new job instance.
     */
    public function __construct($tblNme, $records)
    {
        $this->tblNme = $tblNme;
        $this->records = $records;
    } 

    /**
     * Execute the job.
     */
    public function handle()
    {
        try{
            $adapter = new UpdateSearchAdapter;

            foreach ($this->records as $record) {
                // Build search index on all records in table
                $adapter->process($this->tblNme, $record->id, $record->srchindex);
            }
        }catch(\Exception $e){
            Log::error($e->getMessage());
        }          
    }

}