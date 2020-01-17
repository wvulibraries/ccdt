<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StopWords extends Model
{
     /**
     * check if word exists in the stopwords table
     * 
     * @return      boolean     
     */     
    public static function isStopWord($word) {
        $response = DB::table('stopwords')
                    ->where('word', '=', $word)
                    ->get();
        return (count($response) == 1);
    }
}
