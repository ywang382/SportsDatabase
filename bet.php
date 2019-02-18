<?php include('public.php');
  include('apiCall.php');
  session_start();
  // Delete expired bet requests
  $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
  mysqli_query($db, "DELETE FROM Bet_Requests WHERE fixture_time - UNIX_TIMESTAMP() < 600; ");
  mysqli_close($db);
  if (!isset($_SESSION['username'])) {
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
<title>SimpleBet - <?php echo"{$_SESSION['username']}"?></title>
<style>
body {font-family: "Lato", sans-serif; position=absolute; left=180px;
background-image: url('https://www.wallpaperup.com/uploads/wallpapers/2013/08/05/128430/168a5d30f4bd323f007c3a3add9472ed.jpg');
background-size: cover;background-attachment:fixed;}
.sidenav {
  height:100%; width:160px; position:fixed; z-index:1; top:0; left:0; background-color:#111; overflow-x:hidden; padding-top:20px;
}
.sidenav a {padding: 6px 8px 6px 16px;text-decoration: none;font-size: 25px;color: #818181;display: block;}
.sidenav a.active {background-color: white; color: black;}
.sidenav a:hover:not(.active) {color: #f1f1f1;}
.header { position: fixed;right:10px;top: 0; width: 10%; background-color:#ccc;color: black;text-align: center;}
.main {margin-left:20%; font-size: 18px; padding: 0px 10px; width:1000px; background-color:white;opacity:0.8;}
</style>
</head>
<body>

<div class="sidenav">
  <br><a href="index.php">Home</a>
  <br><a href="login.php">Login</a>
  <br><a href="database.php">Database</a>
  <br><a href="profile.php" >My Profile</a>
  <br><a href="bet.php" class="active">SimpleBet</a>
  <br><a href="contact.php">Help</a>
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
          <p>Gamble Responsibly! <b><?php echo $_SESSION['username']; ?></b></p>
        <?php
          mysqli_close($db);
          ?>
        <?php endif ?>
        <p><a href="profile.php?logout='1'"><button><b style='color:red;'>LOGOUT</b></button></a></p>
        <?php
           $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
           $result = mysqli_query($db, "SELECT credit FROM users WHERE username='".$_SESSION['username']."';");
           $balance = $result->fetch_row();
           if($balance > 100){
             echo "<p><b>Current Balance</b></p><p style=\"color:green;\"><b>{$balance[0]}</b></p>";
           } else{
             echo "<p><b>Current Balance</b></p><p style=\"color:red;\"><b>{$balance[0]}</b></p>";
           }
         ?>
      </div>
    <h1>SimpleBet </h1>
    <p>On platform betting with virtual credits. Please review the betting <a href='contact.php'>rules</a>.</p>
    <p>You can bet with any users with an registered account by searching for them below: </p>
    <form method="post" action="bet.php">
	  		<label>Username:</label>
	  		<input type="text" name="uname" placeholder="full/partial name"><br>
        <label>Email:</label>
        <input type="email" name="em" placeholder="xx@jhu.edu"><br>
        <button type="submit" class="btn" name="search">Find Users</button>
    </form><br>
<?php
if(isset($_POST['search']) && (!empty($_POST['uname']) || !empty($_POST['em']))){
  $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
  $result = mysqli_query($db, "Call SearchUser('".$_POST['uname']."','".$_POST['em']."');");
  if(mysqli_num_rows($result) == 0){
    echo $msg4;
  } else{
    echo "<table border='2'><tr><th>Users</th></tr>";
    while($row = $result->fetch_row()){
      echo "<tr><td>{$row[0]}</td></tr>";
    }
    echo "</table><a href='bet.php'>Clear Result</a>";
  }
  mysqli_close($db);
}

 ?>

    <h3>Request a Bet</h3>

    <form method="post" action="bet.php">
	  		<label>Username of your opponent:</label>
	  		<input type="text" name="requested" required placeholder="username"><br>
        <label>Fixture ID:</label>
        <input type="number" name="fixtureID" required placeholder="fixture id" min="0"><br>
        <label>Your winning side:</label>
        <select name="winTeam">
          <option value="home">Home Team</option>
          <option value="away">Away Team</option>
        </select><br>
        <label>Bet Amount: </label>
        <input type="number" name="betAmt" placeholder="50" min="10" max="500" step="1" required><br>
        <button type="submit" class="btn" name="request">Request Bet</button>
    </form><br>
<?php
$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (isset($_POST['request'])) {
  $check1 = mysqli_query($db, "SELECT * FROM users WHERE username = '".$_POST['requested']."' and username != '".$_SESSION['username']."';");
  $check2 = mysqli_query($db, "SELECT fixture_id, event_time FROM Fixtures WHERE fixture_id = '".$_POST['fixtureID']."' and event_time > UNIX_TIMESTAMP();");
  if (mysqli_num_rows($check1)==1 && mysqli_num_rows($check2)==1) {
    $row = $check2->fetch_row();
    mysqli_query($db, "INSERT INTO Bet_Requests (requester, winSide, requested, bet_amount, fixture_id, fixture_time)
    values ('".$_SESSION['username']."', '".$_POST['winTeam']."', '".$_POST['requested']."', '".$_POST['betAmt']."','".$row[0]."', '".$row[1]."');");
  }else {
    echo $error8;
  }
}
if(isset($_POST['del_req'])){
  mysqli_query($db, "DELETE FROM Bet_Requests WHERE requester = '".$_SESSION['username']."' and request_id = '".$_POST['rID']."';");
  header('location:bet.php');
}
if(isset($_POST['rej_req'])){
  mysqli_query($db, "DELETE FROM Bet_Requests WHERE requested = '".$_SESSION['username']."' and request_id = '".$_POST['rejectID']."';");
  header('location:bet.php');
}


echo "<h3>Your Requested Bets</h3>";
$result = mysqli_query($db, "CALL ShowBetRequests('".$_SESSION['username']."');");
if(mysqli_num_rows($result)==0){
  echo $msg5;
} else {
  echo "<table border='2'>";
  echo "<tr><th>Request ID</th><th>Bet Amount</th><th>Opponent</th><th>Your Winning Team</th><th>Fixture ID</th><th>Match Start</th><th>Home Team</th><th>Away Team</th><th>Match Status</th></tr>";
  while ($row = $result->fetch_row()) {
    $string = substr(str_replace("T"," ", $row[5]),0,19);
    echo "<tr><th>{$row[0]}</th><td>{$row[1]}</td><td>{$row[2]}</td><th>{$row[3]}</th><td>{$row[4]}</td><td>{$string}</td><td>{$row[6]}</td><td>{$row[7]}</td><td>{$row[8]}</td></tr>";
  }
  echo "</table>";
}
mysqli_close($db);
?>
<?php if (mysqli_num_rows($result)!=0) : ?>
  <form method='post'>
      <label>Delete Request:</label>
      <input type='number' min='0' name='rID' placeholder='Request ID'>
      <button type="submit" name='del_req'>DELETE</button><br>
  </form>
<?php endif ?>

<?php

$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
echo "<br><h3>Bets Waiting Your Approval</h3>";
$result = mysqli_query($db, "CALL ShowRequestedBets('".$_SESSION['username']."');");
if(mysqli_num_rows($result)==0){
  echo $msg6;
} else {
  echo "<table border='2'>";
  echo "<tr><th>Request ID</th><th>Bet Amount</th><th>Opponent</th><th>Your Winning Team</th><th>Fixture ID</th><th>Match Start</th><th>Home Team</th><th>Away Team</th><th>Match Status</th></tr>";
  while ($row = $result->fetch_row()) {
    $string = substr(str_replace("T"," ", $row[5]),0,19);
    echo "<tr><th>{$row[0]}</th><td>{$row[1]}</td><td>{$row[2]}</td><th>{$row[3]}</th><td>{$row[4]}</td><td>{$string}</td><td>{$row[6]}</td><td>{$row[7]}</td><td>{$row[8]}</td></tr>";
  }
  echo "</table>";
}
?>
<?php if (mysqli_num_rows($result)!=0) : ?>
  <form method='post'>
      <label>Accept Request:</label>
      <input type='number' min='0' name='acceptID' placeholder='Request ID'>
      <button type="submit" name='acc_req'>ACCEPT</button><br>
      <label>Reject Request:</label>
      <input type='number' min='0' name='rejectID' placeholder='Request ID'>
      <button type="submit" name='rej_req'>REJECT</button><br>
  </form>
<?php endif ?>
<?php
$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
if (isset($_POST['acc_req'])) {
  $check = mysqli_query($db, "SELECT * FROM Bet_Requests WHERE request_id = '".$_POST['acceptID']."';");
  if (mysqli_num_rows($check)==1) {
    mysqli_query($db, "CALL PlaceBets('".$_POST['acceptID']."');");
    header('location:bet.php');
  }else {
    echo $error9;
  }
}
mysqli_close($db);

echo "<br><h3>Your Placed Bets</h3>";
$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
$result = mysqli_query($db, "CALL ShowBets('".$_SESSION['username']."');");
if(mysqli_num_rows($result)==0){
  echo $msg7;
} else {
  echo "<table border='2'>";
  echo "<tr><th>Bet ID</th><th>Bet Amount</th><th>Opponent</th><th>Your Winning Team</th><th>Fixture ID</th><th>Match Start</th><th>Home Team</th><th>Away Team</th><th>Match Status</th></tr>";
  while ($row = $result->fetch_row()) {
    $string = substr(str_replace("T"," ", $row[5]),0,19);
    echo "<tr><th>{$row[0]}</th><td>{$row[1]}</td><td>{$row[2]}</td><th>{$row[3]}</th><td>{$row[4]}</td><td>{$string}</td><td>{$row[6]}</td><td>{$row[7]}</td><td>{$row[8]}</td></tr>";
  }
  echo "</table>";
}
mysqli_close($db);

echo "<br><h3>Your Transaction History</h3>";
$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
$result = mysqli_query($db, "Call ShowTransact('".$_SESSION['username']."');");
if(mysqli_num_rows($result)==0){
  echo $msg8;
} else {
  echo "<table border='2'>";
  echo "<tr><th>Fixture ID</th><th>Transaction Time</th><th>Amount</th><th>From</th><th>To</th><th>Status</th></tr>";
  while ($row = $result->fetch_row()) {
    $string = date('m/d/Y H:i:s', $row[1]);
    echo "<tr><td>{$row[0]}</td><td>{$string}</td><th>{$row[2]}</th><td>{$row[3]}</td><td>{$row[4]}</td>";
    if($row[5] == "Win"){
      echo "<th style=\"color:green;\">{$row[5]}</th></tr>";
    } else {
      echo "<th style=\"color:red;\">{$row[5]}</th></tr>";
    }
  }
  echo "</table>";
}
 ?>

</div>
</body>
</html>
