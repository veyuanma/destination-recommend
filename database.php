<?php
$mysql_server_name="myeeb542.c2e3gz3tqbsa.us-west-1.rds.amazonaws.com:3306" ;
$mysql_username="myeeb542"; 
$mysql_password="myeeb542";
$mysql_database="myeeb542";

$con = mysql_connect($mysql_server_name,$mysql_username,$mysql_password); // connect to database
   
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
  
/*$con = mysqli_connect('myeeb542.c2e3gz3tqbsa.us-west-1.rds.amazonaws.com', 'myeeb542', 'myeeb542', 'myeeb542', 3306);	
if (mysqli_connect_errno($con))
 {
 echo "Failed to connect to MySQL: " . mysqli_connect_error();
 } 
 */
 echo "success";
 echo "<br />";
 
/*if (mysql_query("CREATE DATABASE my_rds ",$con))
  {
  echo "Database created";
  echo "<br />";
  }
else
  {
  echo "Error creating database: " . mysql_error();
  }

// Create table in database
mysql_select_db("my_rds", $con);
$sql = "CREATE TABLE Information 
(
PlaceID varchar(45),
VisitedTimes int
)";
mysql_query($sql,$con);
*/

// insert 
$placeID = "164276";
$times = 3;
//$query = "INSERT INTO Information "."(PlaceID,VisitedTimes) ". "VALUES "."('$placeID','$times')";
$query2 = "INSERT INTO Information "."(PlaceID,VisitedTimes) ". "VALUES "."('88ubhba9,'8')";
$query3 = "INSERT INTO Information "."(PlaceID,VisitedTimes) ". "VALUES "."('228rbhaf','12')";
$query4 = "INSERT INTO Information "."(PlaceID,VisitedTimes) ". "VALUES "."('0083sdvsap','30')";
mysql_select_db('my_rds');
mysql_query( $query, $con);
mysql_query( $query2, $con);
mysql_query( $query3, $con);
mysql_query( $query4, $con);

/*if(!$retval )
{
  die('Could not enter data: ' . mysql_error());
}
echo "Entered data successfully";
*/
 //select
mysql_select_db("my_rds", $con);
$qu= "SELECT * FROM Information where PlaceID ='$placeID' and VisitedTimes ='$times'";
$result= mysql_query($qu, $con);
if(mysql_num_rows($result)==0){
$newrow= "INSERT INTO Information "."(PlaceID,VisitedTimes) ". "VALUES "."('$placeID','1')";
mysql_query($newrow,$con);
 }

else{
while($row = mysql_fetch_array($result))
  {
  echo $row['VisitedTimes'];
  echo "<br />";
 }
 }
 
 
 
 //update
mysql_select_db("my_rds", $con);
mysql_query("UPDATE Information SET VisitedTimes = '36'
WHERE PlaceID = '$placeID' AND VisitedTimes = '$times'");
 
 
 
 
 
 
 

?>
