<?php include('server.php') ?>
<!DOCTYPE html>
<html>
	
<head>
  <title>Registration system PHP and MySQL</title>
  <link rel="stylesheet" href="style_logowanie.css">
</head>




<body>	 
  <form method="post" action="login.php">
  	<?php include('errors.php'); ?>
  	<div class="input-group">
  		<label>Username</label>
  		<input type="text" name="username" >
  	</div>
  	<div class="input-group">
  		<label>Password</label>
  		<input type="password" name="password">
  	</div>
  	<div class="input-group">
  		<button type="submit" class="btn" name="login_user">Login</button>
  	</div>
  	<p>
  		Not yet a member? <a href="register.php">Sign up</a>
  	</p>
	  <p>Nie pamietasz hasła? <a href="reset_hasla/brak_hasla.html">Reset password</a></p>
  </form>
</body>
</html>