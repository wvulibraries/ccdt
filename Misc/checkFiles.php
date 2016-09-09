#!/usr/bin/php
<?php
/*
* The idea is to check if the files indeed exist in the docuemnts
*/
require_once "dbHeader.php";

// Class for checking the physical files
class CheckFiles{

  // Simple constructor to set the dir variable
  public function __construct($dir){
    $this->dir = $dir;
    $this->hitCount=0;
    $this->missCount=0;
    $this->emptyCount=0;
    $this->total=0;
  }

  // Get all the records from the database
  public function getAllRecrds(){
    try{
      //Define db handle
      $db = new PDO(DB_DRIVER . ":dbname=" . DB_DATABASE . ";host=" . DB_SERVER, DB_USER, DB_PASSWORD);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      //Prepare the sql statement
      $getDocQuery=$db->prepare('SELECT  `ID`,`out_document_name` FROM `correspondence`');
      $getDocQuery->execute();
      //Get the results
      $getDocRslts=$getDocQuery->fetchAll();
      foreach($getDocRslts as $docKey=>$docVal){
        if(empty($docVal['out_document_name'])){
          $this->emptyCount++;
          continue;
        }
        $dName=$this->dirTokenize($docVal['out_document_name']);
        $fPath=$this->dir.$dName;
        //echo $fPath;
        if(file_exists($fPath)){
          $this->hitCount++;
          print "\n---------\n Found: ".$fPath."\t".$docVal['ID'];
        }
        else{
          $this->missCount++;
        }
        $this->total++;
      }
      print "\n\n--------------------------------------------------------------------------------------------------------------------------------------------------------\n\t
      Found: ".$this->hitCount." \t Empty: ".$this->emptyCount." \t Missing: ".$this->missCount." \t Total: ".$this->total."\n";
    }
    catch(Exception $thisEx){
      trigger_error($thisEx->getMessage());
    }
  }

// Tokenize the mysql entry
public function dirTokenize($thisDir){
  $pTkns=explode('\\',strval($thisDir));
  $fChar=array_shift($pTkns);
  $pTkns = array_diff($pTkns,array('BlobExport'));
  $fPath = implode("/",$pTkns);
  return end($pTkns);
}
}

//Initialize and run check
$dir = '/home/www.libraries.wvu.edu/public_html/rockefeller-css/public/documents/indivletters/';
$thisCheck = new CheckFiles($dir);
$thisCheck->getAllRecrds();
