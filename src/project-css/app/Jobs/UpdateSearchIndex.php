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
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class UpdateSearchIndex implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tblNme;
    private $records;    

    /**
     * Create a new job instance.
     *
     * @param string $tblNme
     * @param array $records
     *  
     */
    public function __construct($tblNme, $records)
    {
        $this->tblNme = $tblNme;
        $this->records = $records;
    } 

    /**
     * The handle function contains code to be executed for 
     * the job. 
     * 
     * @return void
     */
    public function handle()
    {
        $adapter = new UpdateSearchAdapter;

        foreach ($this->records as $record) {
            // Build search index on all records in table
            $adapter->process($this->tblNme, $record->id, $record->srchindex);
        }           
    }

}