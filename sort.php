<!DOCTYPE html>
<html>
<body>
<?php
date_default_timezone_set('America/Los_Angeles');

$txt = $_GET['text'];
 $myinfo = $_GET['myinfo'];

//echo $txt;
//echo '<br>';
//echo $myinfo;

 $location_array=explode('|',$myinfo);	
    
    $mylat=$location_array[0];
//echo $mylat;
//echo '<br>';
    $mylng=$location_array[1];
//echo $mylng;
    
    function getDistance($lat1,$lng1,$lat2,$lng2) {
  		$dis = sqrt(pow($lat1-$lat2,2)+pow($lng1-$lng2,2))+0.000001;
		$dis = 1/$dis;
		return $dis;
	}

$mysql_server_name="myeeb542.c2e3gz3tqbsa.us-west-1.rds.amazonaws.com:3306" ;
$mysql_username="myeeb542";
$mysql_password="myeeb542";
$mysql_database="myeeb542";

//	echo 'welecome to php';
   
$con = mysql_connect($mysql_server_name,$mysql_username,$mysql_password); // connect to database

if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

//$Bdistance = $_POST['distance'];
//	$Bprice = $_POST['price'];
$Bdistance = 1;
$Bprice = 1;
//	echo 'Bdistance'.$Bdistance;
//	echo 'Bprice'.$Bprice;

class Place{
		function Place($name, $id, $rating, $distance, $hour, $price, $web, $visited){
			$this->name = $name;
			$this->id = $id;
			$this->rating = $rating;
			$this->distance = $distance;
			$this->hour = $hour;
			$this->price = $price;
			$this->web = $web;
			$this->visited = $visited;
			$this->score = 0;

		}
	}


$place_array = explode('^',$txt); //seperate every place, arrlength is the number of palces returned

//echo count($place_array);
//echo '<br>';

array_pop($place_array);

$arrlength = count($place_array);
//echo $arrlength;

$places = array();      // array of object PLACE  "places"

	$max_hour = 0;
	$min_hour = 1440;
	$max_visited = 0;

	$max_dis=0;
	$min_dis=1000;

	for($i=0; $i<$arrlength; $i++){         //for every place
		$fields = explode('|',$place_array[$i]);   //seperate every fields
		//function Place($name, $id, $rating, $price, $hour, $web)
		//place from client ($name, $id, $rating, $price, $wnd hour,$week hour, $web,$lat, $lng )
		$nodist = 0;
		
		for ($j=0;$j<9;$j++){
			if(strcmp($fields[$j],'undefined')==0){
				if($j==4||$j==5){
					$fields[$j]=-1;
				} else if($j==7||$j==8){
					$nodist=1;
				} else {	
					$fields[$j]=0;
				}	
			}
		}

		$name = $fields[0];
		$id = $fields[1];
		$rating = $fields[2]/5;
		$price = $fields[3]/4;
		//$hour = $fields[4];

		$today =date('N');
		if($today<6){
			$close_time = $fields[5];
		}
		else{
			$close_time = $fields[4];
		}
		$hour = max(((int)$close_time/100 - date('H')),0)*60+max(((int)$close_time%100 - date('i')),0);
//echo date('H');
//echo " ".date('i');
//echo '<br>';
//echo "close time is:";
//echo $close_time;
//echo "    ".date('H').date('i');
//echo '<br>';
//echo (int)$close_time/100 - date('H');
//echo '<br>';
//echo "hour is:";
//echo $hour;
//echo '<br>';
		$web = $fields[6];

		if($nodist==0){
		$distance = getDistance(($mylat),($mylng),($fields[7]),($fields[8]));}
		else{
			$distance = 0;}
	
//echo "lat is:".$fields[7]."lng is:".$fields[8];
//echo '<br>';
//echo "distance is:";
//echo $distance;
//echo '<br>';	
//function Place($name, $id, $rating, $distance, $hour, $price, $web, $visited)

		$place = new Place($name, $id,$rating,$distance, $hour, $price,  $web,0);
//echo count($places);

		array_push($places,$place);

	$max_hour = max($max_hour,$hour);
//echo "max hour is:";
//echo $max_hour;
//echo '<br>';
		$min_hour = min($min_hour,$hour);	
//echo "min hour is:";
//echo $min_hour;
//echo '<br>';
		$max_dis=max($distance,$max_dis);
//echo "max dis is:";
//echo $max_dis;
//echo '<br>';	
	$min_dis=min($distance,$min_dis);
//echo "min dis is:";
//echo $min_dis;
//echo '<br>';
//echo '<br>';
		$visited = 0;
		/*跟数据库的place_id进行比较 如果存在 就把visit提出来*/
	mysql_select_db("my_rds", $con);
		$query= "SELECT * FROM Information WHERE PlaceID ='$id'";
$entry= mysql_query($query);
if(mysql_num_rows($entry)>0){
	while($row = mysql_fetch_array($entry))
  	{
  	$visited = $row['VisitedTimes'];
  	//echo $row['VisitedTimes'];
  	//echo "<br />";

 	}
}
//echo "visited first is:".$visited."!!!!!!!!!!!!!!!!!!!!!!!!"."<br><br>";
//echo $id."this is id...........";

		$max_visited = max($max_visited,$visited);
		//function Place($name, $id, $rating, $distance, $hour, $price, $web, $visited)
                $place = new Place($name, $id,$rating,$distance, $hour, $price,  $web,$visited);
		//echo count($places);

                array_push($places,$place);		
	}

//	echo "count places:";

//echo count($places);
//echo "<br>";
//echo $max_dis."<br>";
//echo $min_dis;
//echo "<br>";

//echo $Bdistance==1;
//echo '<br>';
//echo $Bprice==1;
//echo '<br>';
//echo "end";

$pair = array();

	for($i=0; $i<count($places); $i++){
$place1 = $places[$i];
$place1->hour = ($place1->hour-$min_hour)/($max_hour-$min_hour+1);
if($max_visited>0)
	$place1->visited = $places[$i]->visited/$max_visited;
//echo '<br>';
//echo $place1->visited;
//echo '<br>';
//echo "before normal:".$place1->distance."<br>";
//echo "max dis is:".$max_dis;
//echo '<br>';
        $min_dis=min($distance,$min_dis);
//echo "min dis is:".$min_dis;
//echo '<br>';
//echo ($place1->distance-$min_dis)."<br>";
//echo ($max_dis-$min_dis+0.000000001)."<br>";
		$place1->distance=($place1->distance-$min_dis+0.0000000001)/($max_dis-$min_dis+0.000000001);
//echo "distance is:".$place1->distance."<br>";

//if (empty($_POST["website"])) 
	//	if(empty($Bdistance) && empty($Bprice)){
	if(($Bdistance!=1)&&($Bprice!=1)){	
		$place1->score = 0.4 * $place1->rate + 0.3 * $place1->hour + 0.3 * $place1->visited;
		}
		else if(($Bdistance!=1)&&($Bprice==1)){
			$place1->score = 0.3 * $place1->rate + 0.3 * $place1->price + 0.3 * $place1->hour + 0.1 * $place1->visited;
		}
		else if (($Bdistance==1)&&($Bprice!=1)){
			$place1->score = 0.3 * $place1->rate + 0.3 * $place1->distance + 0.3 * $place1->hour + 0.1 * $place1->visited;
		}
		else{
			$place1->score = 0.3 * $place1->rate + 0.2 * $place1->distance + 0.2 * $place1->hour + 0.2 * $place1->price +0.1 * $place1->visited;
		}

//echo "score is :".$place1->score."<br>";
		array_push($pair, $place1->score);
print_r($place1);
//echo "<br><br>";
//echo '<br>';
	}


arsort($pair);
//echo "<br>";

//result=array();

foreach($pair as $x=>$x_value)
    {
//    	echo "Key=" . $x . ", Value=" . $x_value;    
//	result = array_push($x=>$x_value);
//	echo "<br>";
	break;
    }
print_r($pair);
//echo "<br>";
  //$outcome=$pair[0];
$outcome = reset($pair);
$key = array_search($outcome,$pair);
//echo $outcome;
//echo "<br>";
//echo $key;
//echo "<br>";

echo $places[$key]->name."<br>";
echo $places[$key]->id."<br>";
echo $places[$key]->rating."<br>";
echo $places[$key]->distance."<br>";
echo $places[$key]->hour."<br>";
echo $places[$key]->price."<br>";
echo $places[$key]->web."<br>";
echo $places[$key]->score."<br>";

$place_id = $places[$key]->id;
$times =$places[$key]->visited * $max_visited; 
//echo $times;
$times=$times+1;
//echo "add 1 to visited~~~~~~~~~~~";
//echo $times;
//echo "<br><br>";

if($places[$key]->visited == 0){
	// insert
mysql_select_db('my_rds',$con);
//mysql_query( $query, $con);
mysql_query("INSERT INTO Information "."(PlaceID,VisitedTimes) ". "VALUES "."('$place_id','1')");
} else {
	// update
	
	mysql_select_db("my_rds", $con);
//$visited=$visited+1;
//echo "visited is now!!!!!!";
//echo $times;
//echo "<br><br>";
mysql_query("UPDATE Information SET VisitedTimes = '$times' WHERE PlaceID = '$place_id' ");
}


$outcome = $places[$key]->web;

  //   zheng($outcome->website);
//echo "<br><br><br>";
/*
	echo $outcome->name."<br>";
	echo $outcome->id."<br>";
	echo $outcome->rating."<br>";
	echo $outcome->hour."<br>";
	echo $outcome->price."<br>";
	echo $outcome->web."<br>";
echo "end";
*/

$show=`python crawl.py $outcome`;
//echo $show;
echo `cat pic_txt/content.txt`;
$log = fopen("log","a");
$size = `du pic_txt | grep total`;
fwrite($log,$size);
fclose($log);
echo($size);

?>


</body>
</html>
