<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Collection;
use App\Helpers\CollectionHelper;

/**
 * The controller is responsible for uploading
 * files to the collection.
 * 
 * @author Ajay Krishna Teja Kavur
 * @author Tracy A McCormick <tam0013@mail.wvu.edu>
 */
class UploadController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the table index page
     * 
     * @param integer $cmsID
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */
    public function index($colID) {
        // Get collection
        $thisClctn = Collection::find($colID);

        // return the index page
        return view('admin/upload')->with('cmsID', $thisClctn->id)
                                   ->with('clctnName', $thisClctn->clctnName);
    }

    /**
     * Store Files
     *
     * @param Request $request
     * @param integer $colID
     * 
     * @author Tracy A McCormick
     * @return \Illuminate\Http\Response
     */
    public function storeFiles(Request $request, $colID) {
        // set messages array to empty
        $messages = [];

        $thisClctn = Collection::findorFail($colID);
      
        // Request the file input named 'attachments'
        $files = $request->file('attachments');
        $upFldNme = $request->upFldNme;

        //If the array is not empty
        if ($files[ 0 ] != '') {
          foreach ($files as $file) {
            // Set the destination path
            $destinationPath = $thisClctn->clctnName.'/'.$upFldNme;

            Storage::makeDirectory($destinationPath);
            // Get the orginal filname or create the filename of your choice
            $filename = $file->getClientOriginalName();

            if (Storage::exists($destinationPath.'/'.$filename)) {
              $message = [ 'content' =>  $filename.' Already Exists', 'level' =>  'info' ];
            } else {
               // Copy the file in our upload folder
               $file->storeAs($destinationPath, $filename);

               $message = [
                  'content'  =>  $filename.' Upload successful',
                  'level'    =>  'success',
               ];
            }
            array_push($messages, $message);
          }
        }
        else {
          $message = [ 'content' => ' Error: No Files Attached', 'level' => 'warning' ];
          array_push($messages, $message);
        }

        session()->flash('messages', $messages);
        return view('admin/upload')->with('clctnName', $thisClctn->clctnName)
                                    ->with('cmsID', $thisClctn->id);
    }

}
