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

    /**
     * Get description of the current table
     * Remove the id and timestamps
     * return the remaining fields
     * 
     * @return array of fields  
     */     
    public function getDescription() {
        // Get Description of table
        $results = DB::select("DESCRIBE `{$this->tblNme}`");

        // remove first item we do not change the id of the table
        unset($results[0]);

        // remmove the srchindex and timestamp fields we do not change these
        return array_slice($results, 0, -3);
    }

    /**
     * Get description of the current table
     * Remove the id and timestamps
     * return the remaining fields
     * 
     * @param string $col (field description 
     * that was returned from the DESCRIBE 'tablename' 
     * command)
     * 
     * @return array of fields  
     */     
    public function findFieldType($col) {
        // get varchar size
        preg_match_all('!\d+!', $col->Type, $size);

        // if size was found then determine type
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
            }
        }
        // if size isn't present then field is a text field
        return (new TableHelper)->setText($col->Type);      
    }

    /**
     * Return field type for column name passed
     * 
     * @param string $name (name of field)
     * 
     * @return array (example ['type' => 'text', 'size' => 'medium'])  
     */       
    public function getColumnType($name) {
        // Get Description of table
        $columns = DB::select("DESCRIBE `{$this->tblNme}`");
        
        // loop over table fields
        foreach ($columns as $col) { 
            // return type once we have a match           
            if ($name == $col->Field) {  
                return $this->findFieldType($col);
            }
        }
        // return false if field not found
        return false;
    }

     /**
      * inserts a new record into the table
      * 
      * @return array $schema (multidimensional array contains list
      * of field types for each column name)
      */ 
    public function getSchema() {
      // Set Empty Field Type Array
      $schema = [];

      // Get Description of table
      $columns = $this->getDescription();

      // loop over table fields
      foreach ($columns as $col) {
        array_push($schema, [$col->Field => $this->findFieldType($col)]);
      }     

      // return schema
      return $schema;
    }

    /**
     * inserts a new record into the table
     * 
     * @param array $curArry (array containing new record values)
     */      
    public function insertRecord($curArry) {
        // Insert them into DB
        DB::table($this->tblNme)->insert($curArry);
    }

     /**
      * performs full text query on table 
      * 
      * @param string $search (search text)
      * @param integer $page (Page Number to be displayed)
      * @param integer $perPage (Records displayed per page)
      * 
      * @return array (query results)
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
