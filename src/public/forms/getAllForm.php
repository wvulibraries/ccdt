<style>
.cardCont {
    width: 100%;
    overflow: auto;
}

.card {
  background-color: rgba(240,240,240,1);
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    transition: 0.1s;
    width: 80%;
    border-radius: 5px;
    padding: 5px 10px;
    margin: 10px;
    display: block;
    float: left;
    position: relative;
}

.card:hover {
    box-shadow: 0 15px 20px 0 rgba(0,0,0,0.2);
}

.right {
  float:right;
}

.left {
  float:left;
}

.container {
    padding: 2px 16px;
}
.recId {
  float:right;
  margin: 0px;
  width: 10%;
}
</style>

<?php
//Get the localvars
$localvars  = localvars::getInstance();

//Create the instance for the db connection name
$db = db::get($localvars->get('dbConnectionName'));

//Some variables
$rLimit=20;

//Pagination stuff
//Get the total number of records
$numOfRecQuery="SELECT count(`ID`) as `totRecrds` FROM `correspondence`";
$numQResult=$db->query($numOfRecQuery);
if ($numQResult->error()) {
    throw new Exception("ERROR SQL" . $numQResult->errorMsg());
}
if ($numQResult->rowCount() < 1) {
   $output .=  "<p>No Rows</p>";
}
else{
  $thisRow = $numQResult->fetch();
  $totRecrds= intval($thisRow['totRecrds']);
}
if(isset($_GET['pg'])){
  $curPage = $_GET['pg']+1;
}
else{
  $curPage=0;
}

//Execute the query
$sql = "SELECT * FROM `correspondence` LIMIT 0,$rLimit";
$sqlResult = $db->query($sql);

//Check for any errors
if ($sqlResult->error()) {
    throw new Exception("ERROR SQL" . $sqlResult->errorMsg());

}

//Check if there are any rows
if ($sqlResult->rowCount() < 1) {
   $output .=  "<p>No Rows</p>";
}
else{
  $output='<div class="cardCont">';
  while($row = $sqlResult->fetch()){
    $thisRow = sprintf(
    '<a href="http://www.google.com">
    <div class="card">
      <div class="container">
        <h3 class="recId"><b>%s</b></h3>
        <p><b>Name: </b>%s %s %s %s</p>
        <p><b>Organization: </b>%s</p>
        <p><b>Address: </b>%s<br/>%s, %s, %s<br/>%s<br/>%s</p>
      </div>
    </div>
    </a>',
    htmlSanitize($row['ID']),
    htmlSanitize($row['prefix']),
    htmlSanitize($row['first']),
    htmlSanitize($row['middle']),
    htmlSanitize($row['last']),
    htmlSanitize($row['org']),
    htmlSanitize($row['addr1']),
    htmlSanitize($row['addr2']),
    htmlSanitize($row['City']),
    htmlSanitize($row['state']),
    htmlSanitize($row['zip']),
    htmlSanitize($row['country'])
  );
    $output .= $thisRow;
}
$output.='</div>';
  /*
  $output = "<table border='1'>
                  <tr>
                      <th>
                          <p>ID</p>
                      </th>
                      <th>
                          <p>Prefix</p>
                      </th>
                      <th>
                          <p>first</p>
                      </th>
                  </tr>";
  while($row = $sqlResult->fetch()){
    $thisRow = sprintf(
        '<tr>
            <td>%s</td>
            <td>%s</td>
            <td>%s</td>
        </tr>',
        htmlSanitize($row['ID']),
        htmlSanitize($row['prefix']),
        htmlSanitize($row['first'])

    );
    $output .= $thisRow;
  }
  $output .= "</table>";
  */
}

//Set the results to output variable
$localvars->set('getAllForm', $output);
?>
