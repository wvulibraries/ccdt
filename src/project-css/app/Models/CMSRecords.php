<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CMSRecords extends Model
{
    public static function isCMSRecord($recordtype) {
        $response = DB::table('recordtypes')
                    ->where('tblNme', '=', $recordtype)
                    ->get();
        if (count($response) == 1) {
          return true;
        }
        return false;
    }

    public static function getCMSHeader($recordtype) {
        $response = DB::table('recordtypes')
                    ->where('tblNme', '=', $recordtype)
                    ->get();
        return unserialize($response->fieldNames);
    }
}
