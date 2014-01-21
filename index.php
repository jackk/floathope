<?php 
// @author jack krawczyk -- @jackk
// last updated: Dec 9, 2012

// This is the main marketing page for floathope.

	// includes for php
	include 'includes.php';

	// if a user is logged in, redirect them to the logged in homepage
	if(isset($_COOKIE['USER_floathope']))
	{
		header("Location: home.php");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Float Hope Home</title>
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
	
	<?php getGoogleAnalytics(); ?>
</head>
<body >
	<?php 
		// check if user is logged in
			// if yes, direct to home.php with login
			
		// load the pitch/login page
	?>

	<img class="homepage" src="img/background.jpg" style="z-index:-1000;" />
	
    <div class="container">
    	<div class="row" style="padding-top:40px;">
    	 <div class="span12" style="text-align:center;">
        	<img src='img/hero.jpg' style="padding-bottom: 40px;" /> <br />
        	<p><a class="btn btn-primary btn-large" href="login.php">Get started &raquo;</a></p>
        	<img src='img/howitworks.png' style='padding-top: 20px;' />
      	 </div>
      	</div> 
      	
	</div>
</body>
</html>