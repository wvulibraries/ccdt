<?php
/**
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Helpers\TableHelper;
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
            'fltFleList' => $this->flatFileList(),
            'colID' => 1
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

    public function importCollection($colID) {
        // get shared view data
        $data = $this->sharedViewData;
        
        // set collection id to current
        $data['colID'] = $colID;

        if (Collection::findorFail($colID)->isCms) {
            // return cms import wizard
            return view('admin/wizard/cms')->with($data);
        }

        // return flatfile import wizard
        return view('admin/wizard/flatfile')->with($data);
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
        // 1. Input the table name in the meta Directory
        //Rules for validation
        $rules = array(
            'imprtTblNme' => 'required|unique:tables,tblNme|max:30|min:6|alpha_num',
            'colID' => 'required|Integer',
            'fltFile' => 'required|file|mimetypes:text/plain|mimes:txt,dat,csv,tab',
        );

        //Customize the error messages
        $messages = array(
            'imprtTblNme.required' => 'Please enter a table name',
            'imprtTblNme.unique' => 'The table name has already been taken by current or disabled table',
            'imprtTblNme.max' => 'The table name cannot exceed 30 characters',
            'imprtTblNme.min' => 'The table name should be 6 characters or more',
            'imprtTblNme.alpha_num' => 'The table name can only have alphabets or numbers without spaces',
            'colID.required' => 'Please select a collection',
            'colID.Integer' => 'Please select an existing collection',
            'fltFile.required' => 'Please select a valid flat file',
            'fltFile.file' => 'Please select a valid flat file',
            'fltFile.mimetypes' => 'The flat file must be a file of type: text/plain.',
            'fltFile.mimes' => 'The flat file must have an extension: txt, dat, csv, tab.',
        );

        // Validate the request before storing the data
        $this->validate($request, $rules, $messages);

        $data = [
            'strDir' => $this->strDir,
            'colID' => $request->colID,
            'fltFile' => $request->fltFile,
            'cms' => false,
            'tableName' => $request->imprtTblNme
        ];

        $errors = (new TableHelper)->storeUploadAndImport($data);
        return redirect()->route('tableIndex')->withErrors($errors);
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
        // 1. Get the file name and validate file, if not validated remove it
        //Rules for validation
        $rules = array(
            'slctTblNme' => 'required|unique:tables,tblNme|max:30|min:6|alpha_num',
            'colID2' => 'required|Integer',
            'fltFile2' => 'required|string',
        );

        //Customize the error messages
        $messages = array(
            'slctTblNme.required' => 'Please enter a table name',
            'slctTblNme.unique' => 'The table name has already been taken by current or disabled table',
            'slctTblNme.max' => 'The table name cannot exceed 30 characters',
            'slctTblNme.min' => 'The table name should be 6 characters or more',
            'slctTblNme.alpha_num' => 'The table name can only have alphabets or numbers without spaces',
            'colID2.required' => 'Please select a collection',
            'colID2.Integer' => 'Please select an existing collection',
            'fltFile2.required' => 'Please select a valid flat file',
            'fltFile2.string' => 'Please select a valid flat file',
        );

        // Validate the request before storing the data
        $this->validate($request, $rules, $messages);

        $data = [
            'strDir' => $this->strDir,
            'colID' => $request->colID2,
            'fltFile' => $request->fltFile2,
            'cms' => false,
            'tableName' => $request->slctTblNme
        ];

        // Call helper to create table and dispatch job for import
        // Routine returns array of errors if their is any.
        $errors = (new TableHelper)->selectFileAndImport($data); 
        return redirect()->route('collection.show', ['colID' => $data['colID']])->withErrors($errors);      
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
        // 1. Input the table name in the meta Directory
        //Rules for validation
        $rules = array(
            'colID' => 'required|Integer',
            'cmsFile' => 'required|file|mimetypes:text/plain|mimes:txt,dat,csv,tab',
        );

        //Customize the error messages
        $messages = array(
            'colID.required' => 'Please select a collection',
            'colID.Integer' => 'Please select an existing collection',
            'cmsFile.required' => 'Please select a valid cms file',
            'cmsFile.file' => 'Please select a valid cms file',
            'cmsFile.mimetypes' => 'The cms file must be a file of type: text/plain.',
            'cmsFile.mimes' => 'The cms file must have an extension: txt, dat, csv, tab.',
        );

        // Validate the request before storing the data
        $this->validate($request, $rules, $messages);

        $data = [
            'strDir' => $this->strDir,
            'colID' => $request->colID,
            'fltFile' => $request->cmsFile,
            'cms' => true,
            'tableName' => null
        ];

        $errors = (new TableHelper)->storeUploadAndImport($data);
        return redirect()->route('collection.show', ['colID' => $data['colID']])->withErrors($errors); 
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
        // 1. Get the file name and validate file, if not validated remove it
        //Rules for validation
        $rules = array(
            'colID2' => 'required|Integer',
            'cmsFile2' => 'required|string',
        );

        //Customize the error messages
        $messages = array(
            'colID2.required' => 'Please select a collection',
            'colID2.Integer' => 'Please select an existing collection',
            'cmsFile2.required' => 'Please select a valid flat file',
            'cmsFile2.string' => 'Please select a valid flat file',
        );

        // Validate the request before storing the data
        $this->validate($request, $rules, $messages);

        $data = [
            'strDir' => $this->strDir,
            'colID' => $request->colID2,
            'fltFile' => $request->cmsFile2,
            'cms' => true,
            'tableName' => null
        ];

        // call selectFilesAndImport
        $errors = (new TableHelper)->selectFileAndImport($data);
        return redirect()->route('collection.show', ['colID' => $data['colID']])->withErrors($errors);       
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



