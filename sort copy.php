<?php

	$servername = "myEE542";
	$username = "EE542";
	$password = "1118";
	$dbname = "mydb";


	echo 'welecome to php';

	//dbname = mydb; fields: place_id, place_name, visited
	$conn = new mysqli($servername, $username, $password, $dbname);
	if($conn->connect_error){
		echo("Connect failed:" . $conn->connect_error);
	}
	echo "Connected successfully";

	//collect value of input field

	$Bdistance = $_POST['distance'];
	$Bprice = $_POST['price'];

	echo 'Bdistance'.$Bdistance;
	echo 'Bprice'.$Bprice;
	

	/*
	if customer cares about distance and price, consider rating, distance, opening hour, price and visited in the history;
	rate weight:0.3 distance weight:0.2 hour weight:0.2 price weight:0.2 visited:0.1 

	if customers cares about distance, consider rating, distance, opening hour, and visited in the history;
	rate weight:0.3 distance weight:0.3 hour weight:0.3 visited:0.1 

	if customers cares about price, consider rating, opening hour, price and visited in the history;
	rate weight:0.3 hour weight:0.3 price weight:0.3 visited:0.1 

	if cumsotmer cares about nothing, consider rating, opening hour and visited only
	rate weight:0.4 hour weight:0.3 visited:0.3 
	*/
 

	/*建立place class*/
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
			$this->op1 = 0.3 * $rate + 0.2 * $distance + 0.2 * $hour + 0.2 * $price +0.1 * $visited;
			$this->op2 = 0.3 * $rate + 0.3 * $distance + 0.3 * $hour + 0.1 * $visited;
			$this->op3 = 0.3 * $rate + 0.3 * $price + 0.3 * $hour + 0.1 * $visited;
			$this->op4 = 0.4 * $rate + 0.3 * $hour + 0.3 * $visited;
			$this->score = 0;

		}
	}

	echo 'place created\n';


	
	$txt = $_GET['text'];
	$place_array = explode('^',$txt);

	echo $place_array[0]."<br>";
	echo $place_array[1]."<br>";

	$arrlength = count($place_array);

	echo $arrlength."<br>";

	$places = array();

	$max_hour = 0;
	$min_hour = 1440;
	$max_visited = 0;

	for($i=0; $i<arrlength; $i++){
		$fields = explode('|',$place_array[$i]);
		//function Place($name, $id, $rating, $price, $hour, $web)

		for ($j=0;$j<6;$j++){
			if(strcmp($fields[$j],'undefined')==0){
				$fields[$j]=0;
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
		$hour = ((int)$close_time/100 - date('H'))*60+((int)$close_time%100 - date('i'));

		$web = $fields[6];

		$place = new Place($name, $id, $rating, $price, $hour, $web,0);

		array_push($places,$place);
		$max_hour = max($max_hour,$hour);
		$min_hour = min($min_hour,$hour);	

		/*跟数据库的place_id进行比较 如果存在 就把visit提出来
		$sql = "SELECT visited FROM mydb WHERE id = $place_id";
		$result = $conn -> query($sql);
		if ($result > 0){
			$visited = $result;
		}
		$max_visited = max($max_visited,$visited);
*/
	}
$max_visited = 1;
	echo "array get";

	$pair = array();

	for($i=0; $i<count($score); $i++){
		$places[$i]->hour = ($places[$i]->hour-$min_hour)/($max_hour-$min_hour);
		$places[$i]->visited = $places[$i]->visited/$max_visited;
		
		$place1 = $places[$i];

		if(empty($distance) && empty(price)){
			place1->score = place1->op4;

		}
		else if(empty($distance) && !empty(price)){
			place1->score = place1->op3;
		}
		else if (!empty($distance) && empty(price)){
			place1->score = place1->op2;
		}
		else{
			place1->score = place1->op1;
		}
		array_push($pair, place1=>place1->score);

	}
	

	arsort($pair);



	foreach($score as $outcome=>$score)
    	break;
    


    // zheng($outcome->website);

	echo $outcome->name."<br>";
	echo $outcome->id."<br>";
	echo $outcome->rating."<br>";
	echo $outcome->hour."<br>";
	echo $outcome->price."<br>";
	echo $outcome->web."<br>";



	//if the place is chosen, add 1 to "visited"  aka: UPDATA
	$sql_update = "UPDATA mydb SET visited=visited+1 WHERE name = $outcome->name"
	if($conn->query($sql) == TRUE){
		echo "";
	} else {
		echo "Erro:" . $sql . "<br>" . $conn->error;
	}

	$conn->close();

?>
