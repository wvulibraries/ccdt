<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Adapters;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\UpdateSearchIndex;

use App\Adapters\UpdateSearchAdapter;

use Log;

class OptimizeSearchAdapter {
    private $tblNme;

    public function process($tblNme) {
      $this->tblNme = $tblNme;

      DB::table($tblNme)->orderBy('id')->chunk(1000, function ($records) {        
          // using rand split updates between queues
          //dispatch(new UpdateSearchIndex($this->tblNme, $records))->onQueue('indexQueue' . rand(0, 9));  
          dispatch(new UpdateSearchIndex($this->tblNme, $records))->onQueue('indexQueue');  
      });
    }

}