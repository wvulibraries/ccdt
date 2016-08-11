#!/usr/bin/php
<?php
/*
* This method is to read the tab delimited flat files and store them into MySQl database
*/
require_once "dbHeader.php";

class LoadFlatFiles{

  //Some variables for the flat file location
  protected $fileLoc="";
  protected $fileName="";

  //Contructor to read through
  public function __construct($fileLoc,$fileName){
    print "----This program will load all the flat file records into MySQL----\n";
    $this->fileLoc=$fileLoc;
    $this->fileName=$fileName;
  }

  //Reads the file and calls the tokenize function whenever needed
  public function readFlatFile(){

    //Some variables
    $filePath=$this->fileLoc.$this->fileName;
    $lineNumber=1;
    $lineNumMax=20000;

    print "----Reading flat file: ".$filePath."----\n";
    $fileHandle=fopen($filePath,"r") or die("----Couldn't read the file----\n");
    if($fileHandle){
      //ignore header first line
      fgets($fileHandle, 4096);
      //loop through all the lines
      while (($thisLine = fgets($fileHandle, 4096)) !== false){
        //echo "----Line Number\t".$lineNumber."----\n".$thisLine;
        $tkns=$this->tokString($thisLine);
        //Pass it to the database
        $this->insrtToDb($tkns);
        //Impose a limit on number of lines to process
        if($lineNumber>=$lineNumMax){
          break;
        }
        $lineNumber++;
      }
      //Close the handler for efficiency
      fclose($fileHandle);
    }
  }

  //Function that inserts the values into database
  public function insrtToDb($tkns){
    try{
      //Define db handle
      $db = new PDO(DB_DRIVER . ":dbname=" . DB_DATABASE . ";host=" . DB_SERVER, DB_USER, DB_PASSWORD);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      //Define the array
      $dbTknsArray=array('letterID','descr','docName','creatnDate','topics','fillInFields');
      //Create an insert prepared statement
      $insStmnt=$db->prepare("INSERT INTO formLetters(letterID, descr, docName, creatnDate, topics, fillInFields) VALUES (:letterID, :descr, :docName, :creatnDate, :topics, :fillInFields)");
      foreach($tkns as $key=>$value){
        //echo $key.":\t".$value."\t".$dbTknsArray[$key]."\n";
        $insStmnt->bindParam(":{$dbTknsArray[$key]}",$val=(string)$value);
      }

      //Execute the command
      if($insStmnt->execute()){
        //Free resources
        $db=null;
        echo "----Success----";
        }
    }
    catch(Exception $thisEx){
      trigger_error($thisEx->getMessage());
    }
  }

  //Function to tokenize the string and return the parts of the string
  public function tokString($string){
    $tkns=explode("\t", $string);
    foreach($tkns as $key=>$value){
      $value=trim($value);
      if(empty($value))
      {
        $tkns[$key]=NULL;
      }
    }
    return $tkns;
  }

}

//Define the path for your flat file
$folder="/mnt/wvcguide/Sen. Rockefeller CSS Archive/";
$fileName="archiving_FORM_LETTER.dat";
//Call the file load
$loadThis=new LoadFlatFiles($folder,$fileName);
//read through the file
$loadThis->readFlatFile();
