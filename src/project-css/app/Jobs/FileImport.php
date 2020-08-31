<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Jobs;

use App\Adapters\ImportAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class FileImport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tblNme;
    private $fltFlePath;
    private $fltFle;

    /**
     * Create a new job instance.
     *
     * @param string $tblNme
     * @param string $fltFlePath
     * @param string $fltFle
     *  
     */
    public function __construct($tblNme, $fltFlePath, $fltFle)
    {
        $this->tblNme = $tblNme;
        $this->fltFlePath = $fltFlePath;
        $this->fltFle = $fltFle;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $adapter = new ImportAdapter($this->tblNme, $this->fltFlePath, $this->fltFle);
        // import file into table
        $adapter->process();        
    }
}
