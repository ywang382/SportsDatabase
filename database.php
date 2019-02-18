<?php include('public.php');?>
<!DOCTYPE html>
<html>
<head>
<link rel="icon" href="https://1001freedownloads.s3.amazonaws.com/vector/thumb/128584/nicubunu_Soccer_ball.png">
<title>Database Search</title>
<style>
body {font-family: "Lato", sans-serif; position=absolute; left=200px;
background-image: url("https://i.pinimg.com/originals/da/b8/95/dab895a256053f09af23742ebeaed127.jpg"); background-attachment:fixed}
.sidenav {
  height:100%; width:160px; position:fixed; z-index:1; top:0; left:0; background-color:#111; overflow-x:hidden; padding-top:20px;
}
.sidenav a {padding: 6px 8px 6px 16px;text-decoration: none;font-size: 25px;color: #818181;display: block;}
.sidenav a.active {background-color: white; color: black;}
.sidenav a:hover:not(.active) {color: #f1f1f1;}
.header { position: fixed;right:10px;top: 0; width: 10%; background-color:#ccc;color: black;text-align: center;}
.main {margin-left:20%; font-size: 18px; padding: 0px 10px; width:1000px;background-color:white;opacity:0.8;}
th {background-color: #4CAF50;color: white;}
</style>
</head>
<body>

<div class="sidenav">
  <br><a href="index.php">Home</a>
  <br><a href="login.php">Login</a>
  <br><a href="database.php" class="active">Database</a>
  <br><a href="profile.php">My Profile</a>
  <br><a href="bet.php">SimpleBet</a>
  <br><a href="contact.php">Help</a>
</div>

<div class="main">
  <h2>Public Database for Querying</h2>
  <p>You have to apply at least one filter, leave blank the parameters on which you do not want to filter.</p>

  <h3>Filter Matches </h3>

  <form method="post">
      <label>Matches After:</label>
      <input type="date" name="start_time" placeholder="match start time"><br>
      <label>Match Before:</label>
      <input type="date" name="end_time" placeholder="match end time"><br>
      <label>League Name:</label>
      <select name="league_id">
        <option value=""> </option>
        <option value="Premier League">Premier League</option>
        <option value="Serie A">Serie A</option>
        <option value="Primera Division">La Liga Primera</option>
        <option value="Ligue 1">Ligue 1</option>
        <option value="Bundesliga 1">Bundesliga 1</option>
      </select><br>
      <label>Round:</label>
      <input type="number" name="round" min="0" placeholder="round"><br>
      <label>Home Team:</label>
      <input type="text" name="homeTeam" placeholder="full/partial name"><br>
      <label>Away Team:</label>
      <input type="text" name="awayTeam" placeholder="full/partial name"><br>
      <label>Status:</label>
      <select name="status">
        <option value=""> </option>
        <option value="Match Finished">Match Finished</option>
        <option value="Not Started">Not Started</option>
        <option value="Match Postponed">Match Postponed</option>
        <option value="Kick Off">Kick Off</option>
      </select><br>
      <label>Number of Goals Score by Home Team:</label>
      <input type="text" name="goalHomeTeam" placeholder="goals of home team"><br>
      <label>Number of Goals Score by Away Team:</label>
      <input type="text" name="goalAwayTeam" placeholder="goals of away team"><br>
      <label>Final Score:</label>
      <input type="text" name="final_score" placeholder="0 - 2"><br>
      <button type="submit" class="btn" name="set_params2">Set Parameters</button>
  </form><br>

  <?php
  if (isset($_POST['set_params2'])) {
    if(empty($_POST['start_time']) && empty($_POST['end_time']) && empty($_POST['league_id'])
    && empty($_POST['round']) && empty($_POST['homeTeam']) && empty($_POST['awayTeam'])
    && empty($_POST['status']) && empty($_POST['goalAwayTeam']) && empty($_POST['goalHomeTeam'])
    && empty($_POST['final_score'])) {
      echo $msg3;
    } else {
      if(!empty($_POST['start_time'])) {
        $_POST['start_time'] = (string)strtotime($_POST['start_time']);
      }
      if(!empty($_POST['end_time'])) {
        $_POST['end_time'] = (string)strtotime($_POST['end_time']);
      }
      $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
      $result = $mysqli->query("CALL Filter_Fixtures('".$_POST['start_time']."', '".$_POST['end_time']."', '".$_POST['league_id']."'
                            , '".$_POST['round']."', '".$_POST['homeTeam']."', '".$_POST['awayTeam']."', '".$_POST['status']."'
                            , '".$_POST['goalHomeTeam']."', '".$_POST['goalAwayTeam']."', '".$_POST['final_score']."');");
      if($result-> num_rows == 0){
        echo $msg4;
      } else{
        echo "<table border='2'>";
        echo "<tr><th>Fixture ID</th><th>League</th><th>Round</th><th>Match Start</th>
            <th>Home Team</th><th>Goal Home Team</th><th>Away Team</th><th>Goal Away Team</th><th>Status</th><th>Final Score</th></tr>";
        while ($row = $result->fetch_row()) {
          $string = substr(str_replace("T"," ", $row[3]),0,19);
          $round = substr($row[2], strpos($row[2], "-") + 1);
          echo "<tr><th>{$row[0]}</th><td>{$row[1]}</td><td>{$round}</td>
              <td>{$string}</td><td>{$row[4]}</td><th>{$row[5]}</th>
              <td>{$row[6]}</td><th>{$row[7]}</th><td>{$row[8]}</td><th>{$row[9]}</th></tr>";
       }
       echo "</table><br>";
       echo "<a href='database.php'>Clear Result</a>";
       $mysqli->close();
      }
    }
  }
  ?>


    <h3>Filter Players</h3>
    <form method="post">
	  		<label>Team Name:</label>
	  		<input type="text" name="team_name" placeholder="full/partial team name"><br>
        <label>Player Shirt Number:</label>
        <input type="text" name="pnumber" placeholder="player number"><br>
        <label>Player Name:</label>
        <input type="text" name="pname" placeholder="full/partial player name"><br>
        <button type="submit" class="btn" name="set_params">Set Parameters</button>
    </form><br>
<?php

if (isset($_POST['set_params'])) {
    if(empty($_POST['team_name']) && empty($_POST['pnumber']) && empty($_POST['pname'])) {
      echo $msg3;
    } else{
      $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
      $result = $mysqli->query("CALL Filter_Players('".$_POST['team_name']."', '".$_POST['pnumber']."', '".$_POST['pname']."');");
      if($result-> num_rows == 0){
        echo $msg4;
      } else{
        echo "<table border='2'>";
        echo "<tr><th>Team Name</th><th>Shirt Number</th><th>Player Name</th></tr>";
        while ($row = $result->fetch_row()) {
          echo "<tr><th>{$row[0]}</th><td>{$row[1]}</td><td>{$row[2]}</td></tr>";
        }
        echo "</table><br>";
        echo "<a href='database.php'>Clear Result</a>";
        $mysqli->close();
      }
    }
  }
?>

<h3>Filter Leagues</h3>

<form method="post">
    <label>League Name:</label>
    <input type="text" name="league_name" placeholder="full/partial league name"><br>
    <label>League Country:</label>
    <input type="text" name="league_country" placeholder="league country"><br>
    <label>Season:</label>
    <input type="text" name="season" placeholder="season"><br>
    <button type="submit" class="btn" name="set_params1">Set Parameters</button>
</form><br>

<?php
if (isset($_POST['set_params1'])) {
  if(empty($_POST['league_name']) && empty($_POST['league_country']) && empty($_POST['season'])) {
    echo $msg3;
  } else {
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    $result = $mysqli->query("CALL Filter_Leagues('".$_POST['league_name']."', '".$_POST['league_country']."', '".$_POST['season']."');");
    if($result-> num_rows == 0){
      echo $msg4;
    } else{
      echo "<table border='2'>";
      echo "<tr><th>League ID</th><th>Name</th><th>Country</th><th>Season</th>
            <th>Season Start</th><th>Season End</th><th>Standing</th></tr>";
      while ($row = $result->fetch_row()) {
        echo "<tr><th>{$row[0]}</th><td>{$row[1]}</td><td>{$row[2]}</td>
              <td>{$row[3]}</td><td>{$row[4]}</td><td>{$row[5]}</td><td>{$row[6]}</td></tr>";
      }
      echo "</table><br>";
      echo "<a href='database.php'>Clear Result</a>";
      $mysqli->close();
    }
  }
}
?>

<h3>Filter Teams by Performance</h3>

<form method="post">
    <label>Win Percentage Upper Bound:</label>
    <input type="number" name="winp_u" min="0" max="100" step="5"><br>
    <label>Win Percentage Lower Bound:</label>
    <input type="number" name="winp_b" min="0" max="100" step="5"><br>
    <label>Draw Percentage Upper Bound:</label>
    <input type="number" name="drawp_u" min="0" max="100" step="5"><br>
    <label>Draw Percentage Lower Bound:</label>
    <input type="number" name="drawp_b" min="0" max="100" step="5"><br>
    <label>Lose Percentage Upper Bound:</label>
    <input type="number" name="losep_u" min="0" max="100" step="5"><br>
    <label>Lose Percentage Lower Bound:</label>
    <input type="number" name="losep_b" min="0" max="100" step="5"><br>
    <label>Goals For Average Upper Bound:</label>
    <input type="number" name="GFavg_u" min="0" ><br>
    <label>Goals For Average Lower Bound:</label>
    <input type="number" name="GFavg_b" min="0" ><br>
    <label>Goals Against Average Upper Bound:</label>
    <input type="number" name="GAavg_u" min="0" ><br>
    <label>Goals Against Average Lower Bound:</label>
    <input type="number" name="GAavg_b" min="0" ><br>
    <button type="submit" class="btn" name="set_params5">Set Parameters</button>
</form><br>

<?php
if (isset($_POST['set_params5'])) {
  if(empty($_POST['winp_u']) && empty($_POST['winp_b']) && empty($_POST['drawp_u'])
  && empty($_POST['drawp_b']) && empty($_POST['losep_u']) && empty($_POST['losep_b'])
  && empty($_POST['GFavg_u']) && empty($_POST['GFavg_b']) && empty($_POST['GAavg_u'])
  && empty($_POST['GAavg_b'])) {
    echo $msg3;
  } else {
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    $result = $mysqli->query("CALL Filter_Performance('".$_POST['winp_u']."', '".$_POST['winp_b']."', '".$_POST['drawp_u']."'
    , '".$_POST['drawp_b']."', '".$_POST['losep_u']."', '".$_POST['losep_b']."', '".$_POST['GFavg_u']."'
    , '".$_POST['GFavg_b']."', '".$_POST['GAavg_u']."', '".$_POST['GAavg_b']."');");
    if($result-> num_rows == 0){
      echo $msg4;
    } else{
      echo "<table border='2'>";
      echo "<tr><th>Team Name</th><th>Win Percentage</th><th>Draw Percentage</th><th>Lose Percentage</th>
            <th>Goals For Average</th><th>Goals Against Average</th></tr>";
      while ($row = $result->fetch_row()) {
        echo "<tr><th>{$row[0]}</th><td>{$row[1]}</td><td>{$row[2]}</td>
              <td>{$row[3]}</td><td>{$row[4]}</td><td>{$row[5]}</td></tr>";
      }
      echo "</table><br>";
      echo "<a href='database.php'>Clear Result</a>";
      $mysqli->close();
    }
  }
}
?>

<h3>Rank Teams by Stats</h3>

<form method="post">
    <label>League Name:</label>
    <select name="league_name">
      <option value="">All leagues</option>
      <option value="Premier League">Premier League</option>
      <option value="Serie A">Serie A</option>
      <option value="Primera Division">La Liga Primera</option>
      <option value="Ligue 1">Ligue 1</option>
      <option value="Bundesliga 1">Bundesliga 1</option>
    </select><br>
    <label>Filter by MIN or MAX:</label>
    <select name="min_max">
      <option value="MAX">maximum</option>
      <option value="MIX">minimum</option>
    </select><br>
    <label>Stats:</label>
    <select name="stats">
      <option value="Win">Win</option>
      <option value="Lose">Lose</option>
      <option value="Clean Sheet">Clean Sheet</option>
    </select><br>
    <button type="submit" class="btn" name="set_params6">Set Parameters</button>
</form><br>

<?php
if (isset($_POST['set_params6'])) {
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    $result;
    if($_POST['stats'] == 'Win'){
      $result = $mysqli->query("CALL Best_Team('".$_POST['league_name']."', '".$_POST['min_max']."');");
    } else if($_POST['stats'] == 'Lose'){
      $result = $mysqli->query("CALL Worst_Team('".$_POST['league_name']."', '".$_POST['min_max']."');");
    } else {
      $result = $mysqli->query("CALL Clean_Sheet('".$_POST['league_name']."', '".$_POST['min_max']."');");
    }

    if($result-> num_rows == 0){
      echo $msg4;
    } else{
      echo "<table border='2'>";
      echo "<tr><th>League Name</th><th>Team Name</th><th>{$_POST['stats']}</th></tr>";
      while ($row = $result->fetch_row()) {
        echo "<tr><th>{$row[0]}</th><td>{$row[1]}</td><td>{$row[2]}</td></tr>";
      }
      echo "</table><br>";
      echo "<a href='database.php'>Clear Result</a>";
      $mysqli->close();
    }
}
?>
</div>
</body>
</html>
