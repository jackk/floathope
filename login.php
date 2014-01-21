<?php
	include 'includes.php';

	if($_POST['email'] != null && $_POST['password'] != null)
	{
		$loginEmail = $_POST['email'];
		$loginPassword = $_POST['password'];
		
		// connect to database
		$mysqli = connectDB();
		
		$verifyLogin = "SELECT userid, password FROM user WHERE email='" . $loginEmail . "' AND password=PASSWORD('" . $loginPassword . "');";
		
		if($result = $mysqli->query($verifyLogin))
		{
			if($result->num_rows == 0)
				$wrongInfo = true;
			else 
			{
				while($row = $result->fetch_row())
				{
					$userid = $row[0];
					$encryptedPassword = $row[1];
				}
			}
			
			$result->close();
		}
		
		closeDB($mysqli);
		
		$user = getUserFromDB($userid);
		
		// check if user/pw combo is correct
		if(!$wrongInfo)
		{
			$thirtyDays = time() + 60*60*24*30;
			
			setcookie(EMAIL_floathope, htmlspecialchars($loginEmail), $thirtyDays);
			setcookie(KEY_floathope, htmlspecialchars($encryptedPassword), $thirtyDays);
			setcookie(USER_floathope, htmlspecialchars($user->printUserJSON()), $thirtyDays);
			
			header("Location: home.php");
		}
	}
	else
	{
		// user didn't get here via post, check if they're already logged in
		if(! isset($_COOKIE['USER_floathope']))
		{
			// header("Location: index.php");
			
		}
		else 
		{
			header("Location: home.php");
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Float Hope - Login</title>
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
</head>
<body>
	<?php 
		// check if user is logged in
			// if yes, direct to home.php with login
			
		// load the pitch/login page
	?>

	<img class="homepage" src="img/background.jpg" style="z-index:-1000;" />
	
    <div class="container" style="padding-top:20px;">
      	<div class="hero-unit" style="background-color:#fff;">
			<h2>Welcome to FloatHope!</h2>
			<p>Please log in below. <br />
			<?php if($wrongInfo) { echo "<p style='color:#B80000;'><em>Incorrect email / password.</em></p>"; }?>
				<form method="post" action="login.php">
					<fieldset>
						<label for="email">Email:</label>
						<input id="email" name="email" type="text" value="" />
					</fieldset>
					<fieldset>
						<label for="password">Password:</label> <input id="password" name="password" type="password" value="" />
					</fieldset>
					<fieldset>
						<input id="submit" class="btn btn-primary btn-medium" name="submit" type="submit" value="Log In" />
					</fieldset>
				</form>
			<p>Don't Have an Account? <a href="create-account.php">Create one!</a></p>
		</div>
	</div>
</body>
</html>