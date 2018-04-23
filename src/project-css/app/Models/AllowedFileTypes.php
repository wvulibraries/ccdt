<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AllowedFileTypes extends Model
{
    public static function isAllowedType($extension) {
        $response = DB::table('allowedfiletypes')
                    ->where('extension', '=', $extension)
                    ->get();
        if (count($response) == 1) {
          return true;
        }
        return false;
    }
}
