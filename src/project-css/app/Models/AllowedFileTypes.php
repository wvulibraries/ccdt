<?php
/**
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AllowedFileTypes extends Model
{
    /**
     * Queries database to see if the passed filetype 
     * exists. 
     * 
     * @param       string $extension Input string
     * @return      boolean
     */
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
