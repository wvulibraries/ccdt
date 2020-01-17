<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CMSRecords extends Model
{    
    /**
     * Queries database to see if the passed record type 
     * exists. 
     * 
     * @param       string $recordtype Input string
     * @return      boolean
     */
    public static function isCMSRecord($recordtype) {
        $response = DB::table('recordtypes')
                    ->where('recordType', '=', $recordtype)
                    ->get();
        return (count($response) >= 1);
    }
 
    /**
     * return all records matching $recordtype sorted 
     * in descending order 
     * 
     * @param       string $recordtype Input string
     * @return      array
     */    
    public static function getCMSHeader($recordtype) {
        return DB::table('recordtypes')
                    ->where('recordType', '=', $recordtype)
                    ->orderBy('fieldCount', 'desc')
                    ->get();
    }

     /**
     * query recordTypes returning only records with fieldCount
     * equal to what is expected
     * 
     * @param       string $recordtype Input string
     * @param       integer $fieldCount Input integer
     * @return      array
     */      
    public static function findCMSHeader($recordtype, $fieldCount) {
        return DB::table('recordtypes')
                    ->where('recordType', '=', $recordtype)
                    ->having("fieldCount", "=", $fieldCount)
                    ->orderBy('fieldCount', 'asc')
                    ->get();
    }


     /**
     * query recordTypes returning only records with matching
     * record type and cmsID
     * 
     * @param       string $recordtype Input string
     * @param       string $cmsID Input string
     * @return      array
     */  
    public static function findCMSHeaderWithId($recordtype, $cmdID) {
        return DB::table('recordtypes')
                    ->where('recordType', '=', $recordtype)
                    ->having("cmsId", "=", $cmdID)
                    ->get();
    }

     /**
     * query recordTypes returning only records with fieldCount greater
     * than or equal to what is expected
     * 
     * @param       string $recordtype Input string
     * @param       integer $fieldCount Input integer
     * @return      array
     */      
    public static function findClosestCMSHeader($recordtype, $fieldCount) {
        return DB::table('recordtypes')
                    ->where('recordType', '=', $recordtype)
                    ->having("fieldCount", ">=", $fieldCount)
                    ->orderBy('fieldCount', 'asc')
                    ->get();
    }
}
