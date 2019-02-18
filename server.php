<?php
session_start();
include 'public.php';

$username = "";
$email    = "";
$errors = false;


// Register users
if (isset($_POST['reg_user'])) {

  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  if (empty($username)) { 	$errors = true; echo $error2; }
  if (empty($email)) { 	$errors = true;}
  if (empty($password_1)) { 	$errors = true; echo $error3;}
  if ($password_1 != $password_2) {
  		$errors = true;
      echo $error4;
  }

  // check the DB to make sure user does not already exist
  $result = mysqli_query($db, "Call FindUser('".$username."', '".$email."');");
  mysqli_close($db);
  $db = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);
  $user = mysqli_fetch_assoc($result);

  if ($user) {
    if ($user['email'] === $email) {
      	$errors = true;
        echo $error6;
    } else if ($user['username'] === $username) {
      	$errors = true;
        echo $error5;
    }
  }

  // register user if no errors in the form
  if (!$errors) {
  	$password = md5($password_1);
  	mysqli_query($db, "INSERT INTO users VALUES('$username', '$email', '$password', 500)");
  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: profile.php');
  }
}

// Login
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
    echo $error2;
  	$errors = true;
  }
  if (empty($password)) {
  	echo $error3;
    $errors = true;
  }

  if (!$errors) {
  	$password = md5($password);
  	$results = mysqli_query($db, "Call LoginUser('".$username."', '".$password."');");
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['username'] = $username;
  	  $_SESSION['success'] = "You are now logged in";
  	  header('location: profile.php');
  	}else {
  		echo $error1;
  	}
  }
}

?>
