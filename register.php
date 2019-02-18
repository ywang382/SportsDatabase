<!DOCTYPE html>
<html>
<head>
  <title>Register</title>
  <link rel="icon" href="https://1001freedownloads.s3.amazonaws.com/vector/thumb/128584/nicubunu_Soccer_ball.png">
<style>
body {font-family: "Lato", sans-serif; position=absolute; left=200px;
background-image: url("https://i.pinimg.com/originals/cb/da/b4/cbdab43a160f0596979b6e1ddccfc7c8.jpg"); background-size: cover;}
.sidenav {
  height:100%; width:160px; position:fixed; z-index:1; top:0; left:0; background-color:#111; overflow-x:hidden; padding-top:20px;
}
.sidenav a {padding: 6px 8px 6px 16px;text-decoration: none;font-size: 25px;color: #818181;display: block;}
.sidenav a.active {background-color: white; color: black;}
.sidenav a:hover:not(.active) {color: #f1f1f1;}
.main {margin-left:20%; font-size: 18px; padding: 0px 10px; width:1000px;background-color:white;opacity:0.8;}</style>
</head>
<body>
  <div class="sidenav">
    <br><a href="index.php">Home</a>
    <br><a href="login.php">Login</a>
    <br><a href="database.php">Database</a>
    <br><a href="profile.php">My Profile</a>
    <br><a href="bet.php">SimpleBet</a>
    <br><a href="contact.php">Help</a>
  </div>


  <div class="main">
    <h1>Register</h1>
    <form method="post" action="register.php">
  	<div class="input-group mb-3">
  	  <label>Username</label>
  	  <input type="text" name="username" minlength="3" maxlength="20" required value="<?php echo $username; ?>">
  	</div>
  	<div class="input-group mb-3">
  	  <label>Email</label>
  	  <input type="email" name="email" required value="<?php echo $email; ?>">
  	</div>
  	<div class="input-group mb-3">
  	  <label>Password</label>
  	  <input type="password" required name="password_1" minlength="8" maxlength="15">
  	</div>
  	<div class="input-group mb-3">
  	  <label>Confirm password</label>
  	  <input type="password" required name="password_2">
  	</div>
  	<div>
  	  <button type="submit" class="btn" name="reg_user">Register</button>
      <?php include('server.php') ?>
  	</div>
  	<p>
  		Already a member? <a href="login.php">Sign in</a>
  	</p>
  </form>
  </div>

</body>
</html>
