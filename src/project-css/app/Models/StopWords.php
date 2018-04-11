<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StopWords extends Model
{
    public static function isStopWord($word) {
        $response = DB::table('stopwords')
                    ->where('word', '=', $word)
                    ->get();
        if (count($response) == 1) {
          return true;
        }
        return false;
    }
}
