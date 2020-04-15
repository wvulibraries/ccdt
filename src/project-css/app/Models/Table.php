<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TableHelper;

class Table extends Model
{
    /**
    * Define the many to one relationship with App\Collection
    */
    public function collection() {
        // Allow querying the collections
        return $this->belongsTo('App\Models\Collection');
    }

    /**
    * Returns record count of the current table
    */  
    public function recordCount() {
        return DB::table($this->tblNme)->count();
    }

     /**
     * returns 
     * 
     * @param       integer $amount Input integer
     */   
    public function getPage($amount) {
        return DB::table($this->tblNme)->paginate($amount);
    }

    /**
    * Returns the column names as an array
    */  
    public function getColumnList() {
        return Schema::getColumnListing($this->tblNme);
    }

    /**
    * return number of fields
    */    
    public function getOrgCount() {
      // get all column names
      $clmnLst = $this->getColumnList();

      // return number of fields without the id, time stamps and srchIndex
      return (count($clmnLst) - 4);
    }

     /**
     * returns requested record id
     * 
     * @param       integer $id Input integer
     * 
     * @return      object   
     */      
    public function getRecord($id) {
        return DB::table($this->tblNme)
                    ->where('id', '=', $id)
                    ->get();
    }

    // Return field type for column name passed
    public function getColumnType($name) {
        // Get Description of table
        $columns = DB::select("DESCRIBE `{$this->tblNme}`");
        // Return Type for Field
        foreach ($columns as $col) {            
            if ($name == $col->Field) {
                // get varchar size
                preg_match_all('!\d+!', $col->Type, $size);
                if (count($size[0]) == 1) {
                    $type = explode("(", $col->Type, 2);
                    switch ($type[0]) {
                        case 'varchar':
                            return (new TableHelper)->setVarchar((int) $size[0][0]);
                            break;
                        case 'int':
                        case 'mediumint':
                        case 'bigint':
                            return (new TableHelper)->setInteger($type[0]);
                            break;                                      
                    }
                }
                else {
                    // if size isn't present then field is a text field
                    return (new TableHelper)->setText($col->Type);
                }
            }
        }
    }

     /**
     * inserts a new record into the table
     * 
     * @param       array $curArry Input array
     */      
    public function insertRecord($curArry) {
        // Insert them into DB
        DB::table($this->tblNme)->insert($curArry);
    }

     /**
     * performs full text query on table 
     * 
     * @param       string $search Input string
     * @param       integer $page Input integer
     * @param       integer $perPage Input integer
     * 
     * @return      array   
     */      
    public function fullTextQuery($search, $page, $perPage) {        
        return DB::table($this->tblNme)
                ->select('*')
                ->selectRaw("MATCH (srchindex) AGAINST (? IN BOOLEAN MODE) AS relevance_score", [$search])
                ->whereRaw("MATCH (srchindex) AGAINST (? IN BOOLEAN MODE)", [$search])
                ->orderBy('relevance_score', 'desc')
                ->offset(($page - 1) * $perPage)
                ->limit($perPage)
                ->get();
    }
}
