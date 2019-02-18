<?php
include('public.php');
include('apiCall.php');
include('transaction.php');
// Update database
$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
$result = mysqli_query($db, "SELECT * FROM Last_Updated_Time;");
$last_updated_time = 0;
while ($row = $result->fetch_row()){
			$last_updated_time = $row[0];
}
if(time()- $last_updated_time >= 21600){
	retrieve_fixtures($db);
  retrieve_standings($db);
  $time = time();
  $update_time = "UPDATE Last_Updated_Time SET Time = '$time';";
  mysqli_query($db, $update_time);
}
mysqli_close($db);

// Settle bet transactions for finished matches
$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
$query = "SELECT bet_id, user1, winSide1, user2, bet_amount, goalHomeTeam, goalAwayTeam, b.fixture_id FROM Bets as b, Fixtures as f
					WHERE b.fixture_id = f.fixture_id AND f.status ='Match Finished';";
$result = mysqli_query($db, $query);
mysqli_autocommit($db, false); // Turn off autocommit for SQL transactions
while($row = $result->fetch_row()){
	bet_result($row, $db);
}
mysqli_autocommit($db, true);

// Delete all users whose balance is too low
delete_user($db);
mysqli_close($db);
?>
<!DOCTYPE html>
<html>
<head>
<link rel="icon" href="https://1001freedownloads.s3.amazonaws.com/vector/thumb/128584/nicubunu_Soccer_ball.png">
<title>Databases - Final Project</title>
<style>
body {font-family: "Lato", sans-serif; position=absolute; left=200px;
background-image: url("https://i.pinimg.com/originals/da/b8/95/dab895a256053f09af23742ebeaed127.jpg");
background-attachment:fixed}
.sidenav {
  height:100%; width:160px; position:fixed; z-index:1; top:0; left:0; background-color:#111; overflow-x:hidden; padding-top:20px;
}
.sidenav a {padding: 6px 8px 6px 16px;text-decoration: none;font-size: 25px;color: #818181;display: block;}
.sidenav a.active {background-color: white; color: black;}
.sidenav a:hover:not(.active) {color: #f1f1f1;}
.main {margin-left:20%; font-size: 18px; padding: 0px 10px; width:1000px;background-color:white;opacity:0.8;}
</style>
</head>
<body>

<div class="sidenav">
  <br><a href="index.php" class="active">Home</a>
  <br><a href="login.php">Login</a>
  <br><a href="database.php">Database</a>
  <br><a href="profile.php">My Profile</a>
	<br><a href="bet.php">SimpleBet</a>
  <br><a href="contact.php">Help</a>
</div>

<div class="main">
  <h1 style="text-align:center;">Live Soccer Stats Database</h1>
	<p>Statistics of major leagues in England, Germany, Italy, Spain, and France. Updated every 24 hours!</p>
	<p>Login to check out more stats, manage your favorite teams, track your preferred matches, and more!</p>
	<h3>Today's Matches:</h3>
	<p>All time shown in Coordinated Universal Time (UTC)</p>
	<?php
		 $stime = time()-43200;
		 $etime = time()+43200;
		 $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		 $result = $mysqli->query("CALL GetMatch('$stime','$etime');");
		 if($result -> num_rows > 0){
		 	echo "<table border='2'>";
 	 	 	echo "<tr><th>League</th><th>Match Start</th><th>Home</th><th>Away</th><th>Match Status</th><th>Final Score</th></tr>";
 	  	while ($row = $result->fetch_row()) {
		  	$string = substr(str_replace("T"," ", $row[1]),0,19);
 				echo "<tr><td>{$row[0]}</td><td>{$string}</td><td>{$row[2]}</td><td>{$row[3]}</td><td>{$row[4]}</td><th>{$row[5]}</th></tr>";
 	  	}
 	  	echo "</table><br>";
		} else{
			echo "<p>There are no matches today. Note that league matches usually take place during weekends.</p><br>";
		}
	  $mysqli->close();

	 echo "<h3>Current League Table:</h3>";
	 $leagues = array(2=>"English Premier League",8=>"Bundesliga",94=>"Seria A",87=>"Spanish La Liga",4=>"France Ligue 1");
	 foreach($leagues as $id=>$name){
		 echo "<h4>{$name}</h4>";
		 $imagesrc;
		 if($id != 4){
			 $imagesrc = "https://www.api-football.com/public/leagues/{$id}.png";
		 } else{
			 $imagesrc = "https://www.api-football.com/public/leagues/{$id}.svg";
		 }
		 $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
		 $result = $mysqli->query("CALL GetStanding('$id');");
		 echo "<table border='2'>";
		 echo "<img src = {$imagesrc} height='20%', width='20%'>";
		 echo "<tr><th>Rank</th><th>Team</th><th>Games Played</th><th>Win</th><th>Draw</th><th>Lose</th><th>Goals For</th><th>Goals Against</th><th>Goals Difference</th><th>Points</th></tr>";
		 $rank = 1;
		 while ($row = $result->fetch_row()) {
			 echo "<tr><td>{$rank}</td><td>{$row[0]}</td><td>{$row[1]}</td><td>{$row[2]}</td><td>{$row[3]}</td><td>{$row[4]}</td><td>{$row[5]}</td><td>{$row[6]}</td><td>{$row[7]}</td><td>{$row[8]}</td></tr>";
			 $rank++;
	   }
		 $mysqli->close();
		 echo "</table><br>";
	 }
	?>

</div>

</body>
</html>
