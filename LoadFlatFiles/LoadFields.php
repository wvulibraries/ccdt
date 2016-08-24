#!/usr/bin/php

<?php
/*
* Loads all the fields names into db
*/
require_once "dbHeader.php";

// Class will load the column names of the table from the database
class LoadFields{

  //Constructor to initialize
  public function __construct($dbaseName,$tblName){
    print "----This program will load column names of the flat files into MySQL----\n";

    //Set the database and table to choose from
    $this->dbaseName  = $dbaseName;
    $this->tblName    = $tblName;

    //Set the db parameters
    //Define db handle
    $this->db = new PDO(DB_DRIVER . ":dbname=" . DB_DATABASE . ";host=" . DB_SERVER, DB_USER, DB_PASSWORD);
    $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  }

  //Method to insert the listed cols into the db
  public function run(){
    //first get the cols list
    $cols=$this->getCols();

    //Insert Statement
    $insCols=$this->db->prepare("INSERT INTO publicAccess(`fName`) VALUES (:fName);");

    //print for now
    foreach ($cols as $key => $value) {
      $insCols->bindParam(':fName',$val=(string)$value[0]);
      //echo "\n".$key."\t".$value[0]."\n";

      //Execute the command
      if($insCols->execute()){
        echo "----Success----";
        }

    }

    //Free resources
    $this->db=null;
  }

  // Method will list all columns and load into database
  public function getCols(){
    try{

      //Define the array
      $getColQuery=$this->db->prepare("SELECT `COLUMN_NAME`  FROM `INFORMATION_SCHEMA`.`COLUMNS`  WHERE `TABLE_SCHEMA`=:dbaseName AND `TABLE_NAME`=:tblName;");

      //Set the query
      $getColQuery->bindParam(':dbaseName',$this->dbaseName);
      $getColQuery->bindParam('tblName',$this->tblName);

      //Execute the command
      if($getColStmnt=$getColQuery->execute()){

        //Get the results into array
        $thisRow=$getColQuery->fetchAll();

        //Free resources
        echo "----Success----";
        }

        //return the array
        return $thisRow;
    }
    catch(Exception $thisEx){
      trigger_error($thisEx->getMessage());
    }
  }
}

// set the variables
$dbaseName="rockefellercss";
$tblName="correspondence";
//Call the class
$thisRun=new LoadFields($dbaseName,$tblName);
// Run the iterator
$thisRun->run();
