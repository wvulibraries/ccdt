<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Adapters;

use Illuminate\Support\Facades\DB;
use App\Models\Table;

use Log;

/**
 * Search Index Adapter
 * 
 * The Search Index Adapter creates a basic search index on all records
 * in a table by concating all the fields and saving them to srchindex.
 * We do remove the search index and timestamps since they are not required 
 * by the mysql full text search. Also the srchindex will be be optimized by 
 * the updateSearchAdapter.
 *
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 *
 */
class SearchIndexAdapter {
 
    public function process($tblNme) {
      //get table
      $table = Table::where('tblNme', $tblNme)->firstOrFail();

      $clmnLst = $table->getColumnList();

      // remove the id, srchindex and time stamps
      $clmnLst = array_splice($clmnLst, 1, count($clmnLst) - 4);

      // create srchindex by using mysql concat_ws
      $sql = 'UPDATE `'.$tblNme.'` SET srchindex = LOWER(CONCAT_WS(" ", `' . implode('`, `', $clmnLst) . '`))';
      DB::connection()->getPdo()->exec($sql); 
    }

}