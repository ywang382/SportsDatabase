<!DOCTYPE html>
<html>
<head>
  <link rel="icon" href="https://1001freedownloads.s3.amazonaws.com/vector/thumb/128584/nicubunu_Soccer_ball.png">
  <title>Login</title>
<style>
body {font-family: "Lato", sans-serif; position=absolute; left=200px;
background-image: url("https://i.pinimg.com/originals/cb/da/b4/cbdab43a160f0596979b6e1ddccfc7c8.jpg");background-size: cover;}
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
    <br><a href="index.php">Home</a>
    <br><a href="login.php" class="active">Login</a>
    <br><a href="database.php">Database</a>
    <br><a href="profile.php">My Profile</a>
    <br><a href="bet.php">SimpleBet</a>
    <br><a href="contact.php">Help</a>
  </div class="main">
  	<div class="main">
  		<h1>Login</h1>
	  <form method="post" action="login.php">
	  	<div>
	  		<label>Username</label>
	  		<input type="text" required name="username" >
	  	</div>
	  	<div>
	  		<label>Password</label>
	  		<input type="password" required name="password">
	  	</div>
	  	<div>
	  		<button type="submit" class="btn" name="login_user">Login</button>
        <?php include('server.php') ?>
	  	</div>
	  	<p>
	  		Not yet a member? <a href="register.php">Sign up</a>
	  	</p>
	  </form>
	</div>

</body>
</html>
