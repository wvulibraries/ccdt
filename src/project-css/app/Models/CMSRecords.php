<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CMSRecords extends Model
{
    public static function isCMSRecord($recordtype) {
        $response = DB::table('recordtypes')
                    ->where('recordType', '=', $recordtype)
                    ->get();
        return (count($response) >= 1);
    }

    // return all records sorted in descending order
    public static function getCMSHeader($recordtype) {
        return DB::table('recordtypes')
                    ->where('recordType', '=', $recordtype)
                    ->orderBy('fieldCount', 'desc')
                    ->get();
    }

    // query recordTypes returning only records with fieldCount
    // equal to what is expected
    public static function findCMSHeader($recordtype, $fieldCount) {
        return DB::table('recordtypes')
                    ->where('recordType', '=', $recordtype)
                    ->having("fieldCount", "=", $fieldCount)
                    ->orderBy('fieldCount', 'asc')
                    ->get();
    }

    public static function findCMSHeaderWithId($recordtype, $cmdID) {
        return DB::table('recordtypes')
                    ->where('recordType', '=', $recordtype)
                    ->having("cmsId", "=", $cmdID)
                    ->get();
    }

    // query recordTypes returning only records with fieldCount greater
    // than or equal to what is expected
    public static function findClosestCMSHeader($recordtype, $fieldCount) {
        return DB::table('recordtypes')
                    ->where('recordType', '=', $recordtype)
                    ->having("fieldCount", ">=", $fieldCount)
                    ->orderBy('fieldCount', 'asc')
                    ->get();
    }
}
