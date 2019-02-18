<!DOCTYPE html>
<html>
<head>
<link rel="icon" href="https://1001freedownloads.s3.amazonaws.com/vector/thumb/128584/nicubunu_Soccer_ball.png">
<title>Databases - Final Project</title>
<style>
body {font-family: "Lato", sans-serif; position=absolute; left=200px;
background-image: url("https://stmed.net/sites/default/files/soccer-wallpapers-31385-4613102.jpg");
background-attachment:fixed; background-size: cover;}
.sidenav {
  height:100%; width:160px; position:fixed; z-index:1; top:0; left:0; background-color:#111; overflow-x:hidden; padding-top:20px;
}
.sidenav a {padding: 6px 8px 6px 16px;text-decoration: none;font-size: 25px;color: #818181;display: block;}
.sidenav a.active {background-color: white; color: black;}
.sidenav a:hover:not(.active) {color: #f1f1f1;}
.main {margin-left:20%; font-size: 18px; padding: 0px 10px; width:1000px;background-color:white;opacity:0.9;}
</style>
</head>
<body>

<div class="sidenav">
  <br><a href="index.php">Home</a>
  <br><a href="login.php">Login</a>
  <br><a href="database.php">Database</a>
  <br><a href="profile.php">My Profile</a>
	<br><a href="bet.php">SimpleBet</a>
  <br><a href="contact.php"  class="active">Help</a>
</div>

<div class="main">
  <h1 style="text-align:center;">Welcome to Our Live Soccer Database</h1>
  <p style="text-align:center;"><b>Database.415 Final Project</b></p>
	<p style="text-align:center;">By Yuntian Wang and John Xing</p>
	<h3>User Guide:</h3>
    <ul>
        <li>To see basic live soccer statistics such as the matches today and current tables of the most popular European leagues, go to the home page</li>
        <li>To query and filter different types of statistics in our database, go to the database page</li>
        <li>To create your custom team watchlist, track teams' results and future matches, sign up or login and go to My Profile</li>
        <li>To bet against other users on upcoming matches using virtual credits, sign up or login and go to SimpleBet</li>
        <li>SimpleBet uses the same account which you registered for your profile. For security, you will be logged out after 30 minutes of inactivity.</li>
    </ul>
  <h3>SimpleBet:</h3>
  <p>SimpleBet is an on-platform betting systems for registered users using virtual credits. SimpleBet supports the simplest betting scheme:
    "I bet user X that team Y will win in Match Z. "</p>
  <h4>How to Bet: </h4>
  <ol>
      <li>Every user is given 500 in credits when first registered.</li>
      <li>To request a bet with someone, you need to know their username, the fixture you want to bet on, the team that you think will win, and the amount you wish
      to put in to the bet.</li>
      <li>We identify each fixture with a unique fixture ID, which you can either find through your profile or search in the database.</li>
      <li>Your opponent can choose to accept your request if they think your team will lose, or they can choose to simply reject your request.</li>
      <li>If your opponent accept your request, the bet is placed and changes cannot be made.</li>
      <li>After the result of the game comes out, your balance will be automatically added or deducted by the betting amount depending on whether your team won or lost.</li>
  </ol>
  <h4>Betting Rules and Tips: </h4>
  <ul>
      <li>Gamble responsibily, know your odds.</li>
      <li>If your balance in credits drop below 5, your account will be deleted and all your progress and profiles will be lost. You will be forced to register for a new account
      and start over. </li>
      <li>You can only place bets on matches that start in the future.</li>
      <li>All bet requests on a match that have not been accepted/rejected will expire 10 minutes before the start of the match </li>
      <li>If a match ends in a draw, no one will lose or gain credits from the placed bets of that match.</li>
      <li>Try to bet on matches in which the two teams are similar in strength.</li>
      <li>Before betting, check out your team's recent results and form by tracking its performance in your profile.</li>
  </ul><br>
  <h3>Credits: </h3>
  <p>Our thanks go to <a href="https://www.api-football.com/" target='_blank'>api-football.com</a> for their data!</p><br>
  <h3>Source Code: </h3>
  <form method="post">
      <label>Enter password:</label>
      <input type="password" name="pw" required>
      <button type="submit" class="btn" name="get">Get Source Code</button><br>
  </form>
  <br>
  <?php
  $password = "database415";
  if (isset($_POST['get'])) {
    if($_POST['pw'] == $password){
      echo "<a href='log.txt' download target='_blank'>Download</a>";
    } else{
      echo "<p>You don't have permission to the source code!</p>";
    }
  }
  ?>



</div>

</body>
</html>
