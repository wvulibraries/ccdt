<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Libraries\TableHelper;
use Auth;

class WizardController extends Controller
{
    private $strDir;
    private $sharedViewData;

    public function __construct($storageFolder = 'flatfiles') {
      // Protection to make sure this is only accessible to admin
      $this->middleware('admin');

      // Storage directory
      $this->strDir = $storageFolder;

      // Shared View Data is used by both the flatfile and cms
      // functions to correctly render the file import views.
      $this->sharedViewData = [
            'AuthUsr' => Auth::user(),
            'collcntNms' => Collection::all(),
            'fltFleList' => $this->flatFileList()
        ];
    }

   /**
    * Returns view for the Import Wizard Main Page
    *
    * @return view
    */    
    public function import() {
        return view('admin/wizard/import')->with('AuthUsr', Auth::user());
    }

    /**
    * Returns correct view for importing into a collection
    *
    * @return view
    */    
    public function importTable($colID) {
        var_dump(Collection::find($colID));
        die();
    }   

   /**
    * Returns view for the CMS Import
    *
    * @return view
    */      
    public function cms() {
        return view('admin/wizard/cms')->with($this->sharedViewData);
    }

   /**
    * Returns view for the FlatFile Import
    *
    * @return view
    */      
    public function flatfile() {
        return view('admin/wizard/flatfile')->with($this->sharedViewData);
    }

   /**
    * Process form request to create a new table in a collection
    *
    * Prepares an array of values required by storeUploadsAndImport
    * to continue processing the import request.
    *
    * @param request The request object has 2 fields that we require for the import
    * flatFiles should be an array of files and the imprtTblNme (New Table Name)
    *
    * @return redirect
    */       
    public function flatfileUpload(Request $request) {
        $data = [
            'strDir' => $this->strDir,
            'colID' => $request->colID,
            'flatFiles' => $request->flatFiles,
            'cms' => false,
            'tableName' => $request->imprtTblNme
        ];

        return (new TableHelper)->storeUploadsAndImport($data);
    }    

   /**
    * Process form request to create a new table in a collection
    *
    * Prepares an array of values required by selectFilesAndImport
    * to continue processing the import request.
    *
    * @param request The request object has 3 fields that we require for the import
    * flatFiles2 should be an array of files and the slctTblNme (New Table Name)
    *
    * @return redirect
    */       
    public function flatfileSelect(Request $request) {
        $data = [
            'strDir' => $this->strDir,
            'colID' => $request->colID2,
            'flatFiles' => $request->flatFiles2,
            'cms' => false,
            'tableName' => $request->slctTblNme
        ];

        return (new TableHelper)->selectFilesAndImport($data);       
    }

   /**
    * Process form request to create a new cms table in a collection
    *
    * Prepares an array of values required by storeUploadsAndImport
    * to continue processing the import request.
    *
    * @param request The request object has 3 fields that we require for the import
    * colID which is the collection id, cmsdisFiles should be an array of files and 
    * the imprtTblNme (New Table Name)
    *
    * @return redirect
    */      
    public function cmsUpload(Request $request) {
        $data = [
            'strDir' => $this->strDir,
            'colID' => $request->colID,
            'flatFiles' => $request->cmsdisFiles,
            'cms' => true,
            'tableName' => $request->imprtTblNme
        ];

        return (new TableHelper)->storeUploadsAndImport($data);
    }    

   /**
    * Process form request to create a new table in a cms collection
    *
    * Prepares an array of values required by selectFilesAndImport
    * to continue processing the import request.
    *
    * @param request The request object has 3 fields that we require for the import
    * colID2 is the collection ID, cmsdisFiles2 should be an array of files and the 
    * slctTblNme (New Table Name)
    *
    * @return redirect
    */         
    public function cmsSelect(Request $request) {
        $data = [
            'strDir' => $this->strDir,
            'colID' => $request->colID2,
            'flatFiles' => $request->cmsdisFiles2,
            'cms' => true,
            'tableName' => $request->slctTblNme
        ];

        return (new TableHelper)->selectFilesAndImport($data);       
    }      

   /**
    * Get list of current files in the upload folder. Process
    * the array removing the path from the string. 
    *
    * @return array
    */      
    private function flatFileList() {
      // Get the list of files in the directory
      $fltFleList = Storage::allFiles($this->strDir);

      // Format the file names by truncating the dir
      foreach ($fltFleList as $key => $value) {
        // check if directory string exists in the path
        if (str_contains($value, $this->strDir.'/')) {
          // replace the string
          $fltFleList[ $key ] = str_replace($this->strDir.'/', '', $value);
        }
      }   
      
      return $fltFleList;
    }
    
}


