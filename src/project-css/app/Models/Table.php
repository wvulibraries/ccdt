<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Helpers\TableHelper;

/**
 * Table model is used to view, restart and remove jobs.
 * 
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
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
     * @return      integer
     */    
    public function recordCount() {
        return DB::table($this->tblNme)->count();
    }

    /**
     * returns the records from the requested page to
     * be displayed in the view
     * 
     * @param       integer $amount Input integer
     * @return      array of records
     */    
    public function getPage($amount) {
        return DB::table($this->tblNme)->paginate($amount);
    }

    /**
     * Returns the column names as an array
     * @return      array of field names
     */   
    public function getColumnList() {
        return Schema::getColumnListing($this->tblNme);
    }

    /**
     * return number of fields excluding the 4 
     * standard fields (id, time stamps and srchIndex)
     * 
     * @return      integer
     */       
    public function getOrgCount() {
      // get all column names
      $clmnLst = $this->getColumnList();

      // return number of fields without the id, time stamps and srchIndex
      return (count($clmnLst) - 4);
    }

    /**
     * returns requested record id from the
     * current table.
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

        // remove the srchindex and timestamp fields we do not change these
        return array_slice($results, 0, -3);
    }

    /**
     * Return the field type of the $col string
     * Where the $col is one field from the describe
     * 'tablename' function.
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
     * @return boolean (returns false if we don't find the field) 
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
      * creates a multidimensional array 
      * containing the type and size for each field
      * in the table.
      * 
      * @return array
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
     * @return void
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
