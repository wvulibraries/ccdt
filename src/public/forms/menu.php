<style>
input[type=text] {
    width: 130px;
    max-width: 100%;
    box-sizing: border-box;
    border: 2px solid #ccc;
    border-radius: 4px;
    font-size: 16px;
    background-color: white;
    background-position: 10px 10px;
    background-repeat: no-repeat;
    padding: 12px 20px 12px 40px;
    -webkit-transition: width 0.4s ease-in-out;
    transition: width 0.4s ease-in-out;
}

input[type=text]:focus {
    width: 100%;
}

.labelSearch {
  float: left;
}

</style>

<div class="labelSearch">
  <h3>Search:&nbsp; </h3>
</div>

<div class="searchBar">
  <form>
    <br/>
    <input type="text" class="searchBox" name="query" placeholder="Search.."/>
  </form>
</div>

<div class="menuBar">
  <h4><a>Show Only Records with Physical Files</a></h4>
</div>

<?php

// Class for the searching
class CSSSearch{

  // Protected variables
  protected $recIdArr = array();

  // Constructor for the class
  public function __construct(){

    // Get the local variables
    $this->localvars  = localvars::getInstance();

    // Get the db connection
    $this->db = db::get($this->localvars->get('dbConnectionName'));

  }

  // Query function that tokenizes words and compares
  public function query($text){

    // Tokenize the sentence
    $words=explode(" ",$text);

    //span through the sentence
    foreach ($words as $wrdKey => $wrd) {
      $wrd=htmlSanitize($wrd);
      $this->queryEachCol($wrd);
    }
  }

  public function getAllCols(){
    //Get all the columns
    $colQuery="SELECT `COLUMN_NAME` FROM information_schema.columns WHERE `TABLE_NAME`='correspondence'";
    $colResult = $this->db->query($colQuery);
    //Check for any errors
    if ($colResult->error()) {
        throw new Exception("ERROR SQL" . $colResult->errorMsg());
    }
    // Get all columns
    $allCols=$colResult->fetchAll();
    //Set this for accessing in query
    $this->allCols=$allCols;
  }

  public function queryEachCol($wrd){
    // Run query for all columns
    foreach ($this->allCols as $allColsKey => $colName) {
      // Get the column name
      $thisCol = htmlSanitize($colName['COLUMN_NAME']);
      // Query for the value
      $idQuery="SELECT `ID` from `correspondence` WHERE (`$thisCol` LIKE '%".$wrd."%');";
      $idResult=$this->db->query($idQuery);
      // Check for any errors
      if ($idResult->error()) {
          throw new Exception("ERROR SQL" . $idResult->errorMsg());
      }
      // Get the results
      $allQId = $idResult->fetchAll();
      foreach ($allQId as $qKey => $qValue) {
        $thisID=htmlSanitize($qValue['ID']);
        if(!(is_null($thisID))){
          array_push($this->recIdArr,$thisID);
        }
      }
      /*
      $uniqAllQId = array_unique($allQId,$sort_flags=SORT_NUMERIC);
      foreach ($uniqAllQId as $uniQkey => $uniQValue) {
        $thisID=htmlSanitize($uniQValue['ID']);
        $this->showCard($thisID);
        print $thisID;
      }
      */
    }
  }

  public function showAllCards(){
    //Remove duplicates
    $uniqRecIdArr = array_unique($this->recIdArr,$sort_flags=SORT_NUMERIC);
    foreach ($uniqRecIdArr as $uniQkey => $uniQValue) {
      $thisID=htmlSanitize($uniQValue);
      $this->showCard($thisID);
    }
  }

  public function showCard($cardID){
    $cardQuery = "SELECT * FROM `correspondence` WHERE `ID`=$cardID";
    $cardResult = $this->db->query($cardQuery);

    //Check for any errors
    if ($cardResult->error()) {
        throw new Exception("ERROR SQL" . $cardResult->errorMsg());
    }

    //Check if there are any rows
    if ($cardResult->rowCount() < 1) {
       $this->searchOutput .=  "<p>No Rows</p>";
    }
    else{
      $this->searchOutput .= '<div class="cardCont">';
      while($row = $cardResult->fetch()){
        $thisRow = sprintf(
        '<a href="%s?cid=%s">
        <div class="card">
          <div class="container">
            <h3 class="recId"><b>%s</b></h3>
            <p><b>Name: </b>%s %s %s %s</p>
            <p><b>Organization: </b>%s</p>
            <p><b>Address: </b>%s<br/>%s, %s, %s<br/>%s<br/>%s</p>
          </div>
        </div>
        </a>',
        htmlSanitize("card.php"),
        htmlSanitize($row['ID']),
        htmlSanitize($row['ID']),
        htmlSanitize($row['prefix']),
        htmlSanitize($row['first']),
        htmlSanitize($row['middle']),
        htmlSanitize($row['last']),
        htmlSanitize($row['org']),
        htmlSanitize($row['addr1']),
        htmlSanitize($row['addr2']),
        htmlSanitize($row['city']),
        htmlSanitize($row['state']),
        htmlSanitize($row['zip']),
        htmlSanitize($row['country'])
          );
        $this->searchOutput .= $thisRow;
      }
    $this->searchOutput.='</div>';
    }
  }
}


// Get the query
if(isset($_REQUEST['query'])){
   $thisQuery=strval($_REQUEST['query']);
   // Call the search function
   $cssSearch=new CSSSearch();
   //Sanitize
   $thisQuery=htmlSanitize($thisQuery);
   //Get all the columns
   if(!(isset($cssSearch->allCols))){
     $cssSearch->getAllCols();
   }
   # Run the query
   $cssSearch->query($thisQuery);
   # Show the cards
   $cssSearch->showAllCards();
   //Set the search output
   $cssSearch->localvars->set('searchOutput', $cssSearch->searchOutput);
}


 ?>
