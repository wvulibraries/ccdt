<style>
.cardCont {
    width: 100%;
    overflow: auto;
}

.card {
  background-color: rgba(240,240,240,1);
    box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
    transition: 0.1s;
    width: 90%;
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
  font-size: 100%;
}

ul.pagination {
    display: inline-block;
    padding: 0;
    margin: 0;
}

ul.pagination li {display: inline;}

ul.pagination li a {
    color: black;
    float: left;
    padding: 8px 16px;
    text-decoration: none;
    transition: background-color .3s;
    border: 1px solid #ddd;
    font-size: 22px;
}

ul.pagination li a.active {
    background-color: #003366;
    color: white;
    border: 1px solid #003366;
}

ul.pagination li a:hover:not(.active) {background-color: #ddd;}

.pgDiv{
  text-align: center;
}
</style>

<?php
//Get the localvars
$localvars  = localvars::getInstance();

//Create the instance for the db connection name
$db = db::get($localvars->get('dbConnectionName'));

// Number of records to display
$rLimit=25;

//Pagination stuff
//Get the total number of records
$numOfRecQuery="SELECT count(`ID`) as `totRecrds` FROM `correspondence`";
$numQResult=$db->query($numOfRecQuery);
if ($numQResult->error()) {
    throw new Exception("ERROR SQL" . $numQResult->errorMsg());
}
// Check for the row count or else just fetch
if ($numQResult->rowCount() < 1) {
   $output .=  "<p>No Rows</p>";
}
else{
  $thisRow = $numQResult->fetch();
  $totRecrds= intval($thisRow['totRecrds']);
}
// Test to see for the pagination identifier
if(isset($_REQUEST['pg'])){
  $curPage = intval($_REQUEST['pg'])+1;
  $offSt=$rLimit*$curPage;
}
else{
  $curPage=0;
  $offSt=0;
}

//Simple check for the negetive values
if($curPage<0){
  $output .=  "<p>No Rows</p>";
  return;
}


//Execute the query
$sql = "SELECT * FROM `correspondence` LIMIT $offSt,$rLimit";
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
    '<a href="%s?cid=%s">
    <div class="card">
      <div class="container">
        <h1 class="recId"><b>%s</b></h1>
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
    $output .= $thisRow;
}
$output.='</div>';
}

//Set pagination
$pagination .=  "<hr/>";
if($curPage==0){
  $pagination .= sprintf(
  '
  <div class="pgDiv">
  <ul class="pagination">
    <li><a class="active">Page %s</a></li>
    <li><a href="%s?pg=%s">Next »</a></li>
    <li><a href="%s?pg=%s">Last</a></li>
  </ul>
  </div>
  ',
  htmlSanitize($curPage+1),
  htmlSanitize($_PHP_SELF),
  htmlSanitize($curPage),
  htmlSanitize($_PHP_SELF),
  htmlSanitize(($totRecrds/$rLimit)-2)
  );
}
else if($curPage>0){
  $pagination .= sprintf(
  '
  <div class="pgDiv">
  <ul class="pagination">
    <li><a href="%s?pg=-1">First</a></li>
    <li><a href="%s?pg=%s">« Prev</a></li>
    <li><a class="active">Page %s</a></li>
    <li><a href="%s?pg=%s">Next »</a></li>
    <li><a href="%s?pg=%s">Last</a></li>
  </ul>
  </div>
  ',
  htmlSanitize($_PHP_SELF),
  htmlSanitize($_PHP_SELF),
  htmlSanitize($curPage-2),
  htmlSanitize($curPage+1),
  htmlSanitize($_PHP_SELF),
  htmlSanitize($curPage),
  htmlSanitize($_PHP_SELF),
  htmlSanitize(($totRecrds/$rLimit)-2)

  );
}
$pagination .=  "<hr/>";

//Set the results to output variable
$localvars->set('getAllForm', $output);
$localvars->set('pagination', $pagination);
?>
