<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Collection;
use App\User;
use App\Table;
use Auth;
use App\Libraries\ParsePDFDocuments;
use App\Libraries\TikaConvert;

class UploadController extends Controller{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(){
        $this->middleware('auth');
    }

    /**
    * Show the table index page
    */
    public function index($curTable) {
      // Get the table entry in meta table "tables"
      $curTable = Table::find($curTable);
      if(!$curTable->hasAccess){
        return redirect()->route('home')->withErrors(['Table is disabled']);
      }

      // return the index page
      return view('admin/upload')->with('tblNme',$curTable->tblNme)
                                 ->with('tblId',$curTable);
    }

    /**
     *  Store Files
     *
     * @return Redirect
     */
    public function storeFiles(Request $request, $curTable)
    {
      // set messages array to empty
      $messages = [];

      // Get the table entry in meta table "tables"
      $curTable = Table::find($curTable);
      if(!$curTable->hasAccess){
        return redirect()->route('home')->withErrors(['Table is disabled']);
      }

      // Request the file input named 'attachments'
      $files = $request->file('attachments');
      $upFldNme = $request->upFldNme;

      //If the array is not empty
      if ($files[0] != '') {
        foreach($files as $file) {
          // Set the destination path
          $destinationPath = $curTable->tblNme . '/' . $upFldNme;
          Storage::makeDirectory($destinationPath);
          // Get the orginal filname or create the filename of your choice
          $filename = $file->getClientOriginalName();

          if (Storage::exists($destinationPath . '/' . $filename)) {
            $message = ['content' =>  $filename . ' Already Exists', 'level' =>  'info'];
          }
          else {
             // Copy the file in our upload folder
             $file->storeAs($destinationPath,$filename);

             if ($this->checkForSSN($destinationPath . '/' . $filename)) {
              $message = [
                 'content'  =>  'Social Security Number(s) Detected In ' . $filename,
                 'level'    =>  'warning',
               ];
               array_push($messages, $message);
             }
             $message = [
                'content'  =>  $filename . ' Upload successful',
                'level'    =>  'success',
             ];
          }
          array_push($messages, $message);
        }

        session()->flash('messages', $messages);
        return view('admin/upload')->with('tblNme',$curTable->tblNme)
                                   ->with('tblId',$curTable);
      }
      return(false);
    }

    public function checkForSSN($file)
    {
        // get MimeType for the file this will allow us
        // to verify file exists and is the correct type
        $fileMimeType = Storage::getMimeType($file);
        $path = storage_path('app/' . $file);
        switch ($fileMimeType) {
            case 'text/plain':
            case 'message/rfc822':
                 $contents = file_get_contents($path);
                 break;
            case 'application/msword':
            case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
            case 'text/rtf':
                 $contents = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","", (new tikaConvert)->convert($path));
                 break;
            case 'application/pdf':
                 $contents = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/","", (new ParsePDFDocuments)->parsePDF($path));
                 break;
            default:
                 $contents = null;
        }

        // if we have pulled the text from the file next we need to scan for
        // any social security numbers using regex pattern
        if ($contents != null) {
            // finalise the regular expression, matching the whole line
            $pattern = '#\b[0-9]{3}-[0-9]{2}-[0-9]{4}\b#';

            // preg_match_all will return a count if it is greater than
            // 0 we have matches against the SSN pattern and will return
            // a true value
            if(preg_match_all($pattern, $contents, $matches) > 0){
                return(true);
            }

        }
        return(false);
    }
}
