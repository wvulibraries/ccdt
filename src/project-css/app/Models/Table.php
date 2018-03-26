<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $tableId = null;
    protected $tableName = null;
    public function __construct($id = null) {
        $this->tableId = $id;
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
        return $this->find($this->tableId)->tblNme;
      }
      return null;
    }

    public function recordCount() {
      if ($this->isValid()) {
        return \DB::table($this->tableName())->count();
      }
      return 0;
    }

    public function getPage($amount) {
      return \DB::table($this->tableName())->paginate($amount);
    }

    public function getColumnList() {
      return \DB::getSchemaBuilder()->getColumnListing($this->tableName());
    }

    // public function query($search) {
    //
    // }
}
