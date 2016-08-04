#!/usr/bin/php
<?php
/*
* This method is to read the tab delimited flat files and store them into MySQl database
*/

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
    $lineNumMax=1;

    print "----Reading flat file: ".$filePath."----\n";
    $fileHandle=fopen($filePath,"r") or die("----Couldn't read the file----\n");
    if($fileHandle){
      //ignore header first line
      fgets($fileHandle, 4096);
      //loop through all the lines
      while (($thisLine = fgets($fileHandle, 4096)) !== false){
        //echo "----Line Number\t".$lineNumber."----\n".$thisLine;
        $tkns=$this->tokString($thisLine);
        foreach($tkns as $key=>$value){
          echo $key.":\t".$value."\n";
        }
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

  //Function to tokenize the string and return the parts of the string
  public function tokString($string){
    $tkns=explode("\t", $string);
    /*
    foreach($tkns as $key=>$value){
      echo $key.":\t".$value."\n";
    }
    */
    return $tkns;
  }

}

//Call the file load
$loadThis=new LoadFlatFiles("/mnt/wvcguide/Sen. Rockefeller CSS Archive/","archiving_correspondence.dat");
//read through the file
$loadThis->readFlatFile();
