<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Table extends Model
{
    protected $tableId = null;
    protected $tableName = null;
    public function __construct($id = null) {
      if (($id != null) && (is_numeric($id))) {
        $this->tableId = $id;
        $this->tableName = $this->find($this->tableId)->tblNme;
      }
    }

    /**
    * Define the many to one relationship with App\Collection
    */
    public function collection() {
        // Allow querying the collections
        return $this->belongsTo('App\Models\Collection');
    }

    public function isValid() {
        if ($this->find($this->tableId) == null) {
          return false;
        }
        return true;
    }

    public function tableName() {
        if ($this->isValid()) {
          return $this->tableName;
        }
        return null;
    }

    public function recordCount() {
        if ($this->isValid()) {
          return DB::table($this->tableName)->count();
        }
        return 0;
    }

    public function getPage($amount) {
        return DB::table($this->tableName)->paginate($amount);
    }

    public function getColumnList() {
        return DB::getSchemaBuilder()->getColumnListing($this->tableName);
    }

    public function getRecord($id) {
        return DB::table($this->tableName)
                    ->where('id', '=', $id)
                    ->get();
    }

    public function fullTextQuery($search, $page, $perPage) {
        return DB::table($this->tableName)
                ->select('*')
                ->selectRaw("MATCH (srchindex) AGAINST (? IN BOOLEAN MODE) AS relevance_score", [$search])
                ->whereRaw("MATCH (srchindex) AGAINST (? IN BOOLEAN MODE)", [$search])
                ->orderBy('relevance_score', 'desc')
                ->offset($page - 1 * $perPage)
                ->limit($perPage)
                ->get();
    }
}
