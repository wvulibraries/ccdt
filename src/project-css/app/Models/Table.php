<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Table extends Model
{
    /**
    * Define the many to one relationship with App\Collection
    */
    public function collection() {
        // Allow querying the collections
        return $this->belongsTo('App\Models\Collection');
    }

    public function recordCount() {
        return DB::table($this->tblNme)->count();
    }

    public function getPage($amount) {
        return DB::table($this->tblNme)->paginate($amount);
    }

    public function getColumnList() {
        return DB::getSchemaBuilder()->getColumnListing($this->tblNme);
    }

    public function getRecord($id) {
        return DB::table($this->tblNme)
                    ->where('id', '=', $id)
                    ->get();
    }

    public function fullTextQuery($search, $page, $perPage) {
        return DB::table($this->tblNme)
                ->select('*')
                ->selectRaw("MATCH (srchindex) AGAINST (? IN BOOLEAN MODE) AS relevance_score", [$search])
                ->whereRaw("MATCH (srchindex) AGAINST (? IN BOOLEAN MODE)", [$search])
                ->orderBy('relevance_score', 'desc')
                ->offset($page - 1 * $perPage)
                ->limit($perPage)
                ->get();
    }
}
