<?php

namespace App\Jobs;

use App\Adapters\ImportAdapter;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;


/**
 * File Imort Job calls the Import adapter to 
 * import a file into a table.
 * 
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class FileImport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tblNme;
    private $fltFlePath;
    private $fltFle;

    /**
     * Create a new job instance.
     *
     * @param string $tblNme (name of the table)
     * @param string $fltFlePath (path to the file)
     * @param string $fltFle (name of the file)
     *  
     */
    public function __construct($tblNme, $fltFlePath, $fltFle)
    {
        $this->tblNme = $tblNme;
        $this->fltFlePath = $fltFlePath;
        $this->fltFle = $fltFle;
    }

    /**
     * The handle function contains code to be executed for 
     * the job. 
     * 
     * Handle calls the Import Adapter it reads the file and inserts
     * each record into the specified table.
     * 
     * @return void
     */
    public function handle()
    {
        $adapter = new ImportAdapter($this->tblNme, $this->fltFlePath, $this->fltFle);
        // import file into table
        $adapter->process();        
    }
}
