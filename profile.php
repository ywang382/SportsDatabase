<?php include('public.php');
  include('apiCall.php');
  session_start();

  if (!isset($_SESSION['username'])) {
    $_SESSION['msg'] = "You must log in first";
    header('location: login.php');
  }
  if (isset($_GET['logout'])) {
    session_destroy();
    unset($_SESSION['username']);
    header("location: index.php");
  }
  if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
    // last request was more than 30 minutes ago
    session_unset();     // unset $_SESSION variable for the run-time
    session_destroy();   // destroy session data in storage
    unset($_SESSION['username']);
    header("location: login.php");
  }
  $_SESSION['LAST_ACTIVITY'] = time(); // update last activity time stamp
?>

<!DOCTYPE html>
<html>
<head>
<link rel="icon" href="https://1001freedownloads.s3.amazonaws.com/vector/thumb/128584/nicubunu_Soccer_ball.png">
<title><?php echo"{$_SESSION['username']}"?>'s Profile</title>
<style>
body {font-family: "Lato", sans-serif; position=absolute; left=200px;
background-image: url("https://stmed.net/sites/default/files/soccer-wallpapers-31385-3046714.jpg");
background-size: cover;background-attachment:fixed}
.sidenav {
  height:100%; width:160px; position:fixed; z-index:1; top:0; left:0; background-color:#111; overflow-x:hidden; padding-top:20px;
}
.sidenav a {padding: 6px 8px 6px 16px;text-decoration: none;font-size: 25px;color: #818181;display: block;}
.sidenav a.active {background-color: white; color: black;}
.sidenav a:hover:not(.active) {color: #f1f1f1;}
.header { position: fixed;right:10px;top: 0; width: 10%; background-color:#ccc;color: black;text-align: center;}
.main {margin-left:20%; font-size: 18px; padding: 0px 10px; width:1000px;background-color:white;opacity:0.8;}
</style>
</head>
<body>

<div class="sidenav">
  <br><a href="index.php">Home</a>
  <br><a href="login.php">Login</a>
  <br><a href="database.php">Database</a>
  <br><a href="profile.php" class="active">My Profile</a>
  <br><a href="bet.php">SimpleBet</a>
  <br><a href="contact.php">Contact</a>
</div>

<div class="main">
      <!-- notification message -->
      <?php if (isset($_SESSION['success'])) : ?>
        <div class="error success" >
          <h3>
            <?php
              echo $_SESSION['success'];
              unset($_SESSION['success']);
            ?>
          </h3>
        </div>
      <?php endif ?>
      <!-- logged in user information -->
      <div class="header">
        <?php  if (isset($_SESSION['username'])) : ?>
          <p>Welcome <strong><?php echo $_SESSION['username']; ?></strong>!</p>
        <?php
          mysqli_close($db);
          ?>
        <?php endif ?>
        <p><a href="profile.php?logout='1'"><button><b style='color:red;'>LOGOUT</b></button></a></p>
      </div>
    <h3>Your Favorite Teams: </h3>

    <form method="post">
	  		<label>Add Team to Favorite:</label>
	  		<input type="text" name="teamname1" placeholder="Full team name">
	  		<button type="submit" class="btn" name="add_team">Add</button><br>
        <label>Delete Team from Favorite:</label>
        <input type="text" name="teamname2" placeholder="Full team name">
        <button type="submit" class="btn" name="del_team">Delete</button>
    </form><br>
<?php
if (isset($_POST['add_team'])) {
  $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
  $tname = mysqli_real_escape_string($db, $_POST['teamname1']);
  $results = mysqli_query($db, "SELECT * FROM Standings WHERE team_name = '".$_POST['teamname1']."';");
  if (mysqli_num_rows($results) > 0) {
      mysqli_query($db, "INSERT INTO Likes_Teams values ('".$_SESSION['username']."', '".$_POST['teamname1']."');");
      header('location: profile.php');
    }else {
      echo $error7;
    }
  }
  if (isset($_POST['del_team'])) {
    $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
    $tname = mysqli_real_escape_string($db, $_POST['teamname2']);
    $results = mysqli_query($db, "SELECT team FROM Likes_Teams WHERE user='".$_SESSION['username']."' and team='".$_POST['teamname2']."';");
    if (mysqli_num_rows($results) > 0) {
       mysqli_query($db, "DELETE FROM Likes_Teams WHERE user='".$_SESSION['username']."' and team='".$_POST['teamname2']."';");
       header('location: profile.php');
    }else {
       echo $error7;
    }
  }

    $user_teams = array();
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    $result = $mysqli->query("CALL GetUsersTeams('".$_SESSION['username']."');");
    if($result-> num_rows == 0){
      echo $msg1;
    } else{
      echo "<table border='2'>";
      echo "<tr><th>Your Teams</th><th>League</th><th>Country</th></tr>";
      while ($row = $result->fetch_row()) {
        echo "<tr><th>{$row[0]}</th><td>{$row[1]}</td><td>{$row[2]}</td></tr>";
        array_push($user_teams, $row[0]);
      }
      echo "</table><br>";
      $mysqli->close();
    }

  if(!empty($user_teams)){
    echo "<h3>Recent Form</h3>";
    echo "<table border='2'>";
    foreach($user_teams as $team){
      $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
      $result = $mysqli->query("CALL Form('".$team."');");
      echo "<tr><th>{$team}</th>";
      for($i = 0; $i < 5; $i++){
        if($row = $result->fetch_row()) {echo "<td>{$row[0]}</td>";}
      }
      echo "</tr>";
      $mysqli->close();
    }
    echo "</table><br>";

    echo "<h3>Recent Result</h3>";
    echo "<table border='2'><tr><th>Your Team</th><th>Fixture ID</th><th>Match Time</th><th>Home Team</th><th>Away Team</th><th>Final Score</th></tr>";
    $cur_time = time();
    foreach($user_teams as $team){
      $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
      $result = $mysqli->query("CALL GetRecentMatch('".$team."');");
      echo "<tr><th>{$team}</th>";
      if($row = $result->fetch_row()) {
        $string = substr(str_replace("T"," ", $row[1]),0,19);
        echo "<td>{$row[0]}</td><td>{$string}</td><td>{$row[2]}</td><td>{$row[3]}</td><th>{$row[4]}</th>";
      }
      echo "</tr>";
      $mysqli->close();
    }
    echo "</table><br>";

    echo "<h3>Next Match</h3>";
    echo "<table border='2'><tr><th>Your Team</th><th>Fixture ID</th><th>Match Time</th><th>Home Team</th><th>Away Team</th><th>Status</th></tr>";
    $cur_time = time();
    foreach($user_teams as $team){
      $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
      $result = $mysqli->query("CALL GetFutureMatch('".$team."', '$cur_time');");
      echo "<tr><th>{$team}</th>";
      if($row = $result->fetch_row()) {
        $string = substr(str_replace("T"," ", $row[1]),0,19);
        echo "<td>{$row[0]}</td><td>{$string}</td><td>{$row[2]}</td><td>{$row[3]}</td><td>{$row[4]}</td>";
      }
      echo "</tr>";
      $mysqli->close();
    }
    echo "</table><br>";

    echo "<h3>Detailed & Aggregate Stats</h3>";
    echo "<table border='2'><tr><th>Your Team</th><th>Played</th><th>Wins</th><th>Win Percentage</th><th>Draws</th><th>Draw Percentage</th>
    <th>Loses</th><th>Lose Percentage</th><th>Clean Sheets</th><th>Average GoalsFor</th><th>Average GoalsAgainst</th><th>Points</th></tr>";
    foreach($user_teams as $team){
      $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
      $result = $mysqli->query("CALL GetMoreStats('".$team."');");
      if($row = $result->fetch_row()) {
        echo "<tr><th>{$team}</th><td>{$row[0]}</td><td>{$row[1]}</td><td>{$row[2]}</td><td>{$row[3]}</td><td>{$row[4]}</td><td>{$row[5]}</td><td>{$row[6]}</td>
        <td>{$row[7]}</td><td>{$row[8]}</th><td>{$row[9]}</td><td>{$row[10]}</td></tr>";
      }
    }
    echo "</table><br>";

    echo "<h3>Team Squad</h3>";
    foreach($user_teams as $team){
      echo "<h4>{$team}:</h4>";
      $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
      $result = $mysqli->query("CALL GetSquad('".$team."');");
      echo "<table border='2'><tr><th>Name</th><th>Shirt Number</th></tr>";
      $row = $result->fetch_row();
      echo "<tr><th>{$row[1]}</th><th>{$row[0]}</th></tr>";
      while($row = $result->fetch_row()) {
        echo "<tr><td>{$row[1]}</td><td>{$row[0]}</td></tr>";
      }
      echo "</table><br>";
      $mysqli->close();
    }

    echo "<h3>Full Season Schedule</h3>";
    foreach($user_teams as $team){
      echo "<h4>{$team}:</h4>";
      $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
      $result = $mysqli->query("CALL GetAllMatch('".$team."');");
      echo "<table border='2'><tr><th>Fixture ID</th><th>Match Time</th><th>Home Team</th><th>Away Team</th><th>Status</th><th>Final Score</th></tr>";
      while($row = $result->fetch_row()) {
        $string = substr(str_replace("T"," ", $row[1]),0,19);
        echo "<tr><td>{$row[0]}</td><td>{$string}</td><td>{$row[2]}</td><td>{$row[3]}</td><td>{$row[4]}</td><th>{$row[5]}</th></tr>";
      }
      echo "</table><br>";
    }

  }
    ?>
</div>
</body>
</html>
