<?php

$dbhost = "dbase.cs.jhu.edu";
$dbuser = "jxing8";
$dbpass = "trmqyigwnf";
$dbname = "cs41518_jxing8_db";

$db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);


$error1 = "<p style=\"color:red;\"> Wrong username and password combination! </p>";
$error2 = "<p style=\"color:red;\"> Username is required! </p>";
$error3 = "<p style=\"color:red;\"> Password is required! </p>";
$error4 = "<p style=\"color:red;\"> Passwords do not match! </p>";
$error5 = "<p style=\"color:red;\"> Username already exists, please choose another username! </p>";
$error6 = "<p style=\"color:red;\"> You have already signed up using that email, please <a href=\"login.php\">Sign in</a></p>";
$error7 = "<p style=\"color:red;\"> Team not found! </p>";
$error8 = "<p style=\"color:red;\"> Please check the betting rules and make sure your input is valid! </p>";
$error9 = "<p style=\"color:red;\"> Please enter a valid Request ID! </p>";

$msg1 = "<p>You do not have a favorite team yet, add one now to track more stats!</p>";
$msg2 = "<p>No fixtures to show!</p>";
$msg3 = "<p style=\"color:red;\">Please enter at least one parameter!<p>";
$msg4 = "<p>No Match Found!<p>";
$msg5 = "<p>You have not made a bet request to anyone yet.</p>";
$msg6 = "<p>No one has requested you for a bet yet.</p>";
$msg7 = "<p>You currently have no placed bets.</p>";
$msg8 = "<p>No transaction history to show.</p>";

?>
