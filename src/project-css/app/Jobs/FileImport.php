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

class FileImport implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    private $tblNme;
    private $fltFlePath;
    private $fltFle;

    /**
     * Create a new job instance.
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
        try{
            (new ImportAdapter)->process($this->tblNme, $this->fltFlePath, $this->fltFle);
        }catch(\Exception $e){
            Log::error($e->getMessage());
        }          
    }

}
