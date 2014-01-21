<?php 
// @author jack krawczyk -- @jackk
// last updated: Nov 29, 2012

// This is the main frame for the chrome extension. Accessed via iframe in popup.html

	include 'includes.php';
	
	if(isset($_COOKIE['EMAIL_floathope']))
	{
		// parse out userid from userCookie
		$userArray = json_decode(htmlspecialchars_decode($_COOKIE['USER_floathope']));
		$userid = $userArray->{'userid'};
		
		// refresh cookie to not log user out
		refreshCookie($userid);
	}
?>
<html>
<head>
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
	
	<style type="text/css">
	body {
		width: 280px;
		height: 280px;
	}
	</style>
	
	<?php getGoogleAnalytics(); ?>
</head>
<body style='padding:0px;'>
	<div class='container' style="width:270px;">
	<div class='row' style="width:270px;">
	<div style="width:270px;">
	<?php 
		// check if user is logged in
			// if no, direct to index.php
			
		// load the account infomation using the userid from the login cookie
		if(isset($_COOKIE['EMAIL_floathope']))
		{
			/*$thisEmail = $_COOKIE['EMAIL_floathope'];
			echo "Hello, here is your email: <br />" . $thisEmail;
			echo "<p><a href='logout.php'>Log Out</a></p>";*/
			
			echo "<p><a href='http://dev.floathope.com/login.php' target='_blank'>Check my account</a></p>";
			
			// parse out userid from userCookie
			$userArray = json_decode(htmlspecialchars_decode($_COOKIE['USER_floathope']));
			$userid = $userArray->{'userid'};
			
			// refresh cookie to not log user out
			//refreshCookie($userid);
			
			// Get monthly user stats
			$monthlyStats = getMonthlyStats($userid);
			$month = date("F");
			echo "<h3>$month Stats</h3> \n<table class='table table-striped'>
				<thead>
					<tr>
						<th>Indulgence</th>
						<th style='text-align:center;'>Visits</th>
					</tr>
				</thead>
				<tbody>";
			
			$totalCount = 0;
			foreach($monthlyStats as $domain => $count)
			{
				echo "<tr><td>" . $domain . "</td><td style='text-align:center;'>" . number_format($count) . "</td></tr>";
				$totalCount += $count;
			}
			
			echo "</tbody>\n<thead><tr><th>Total</th><th style='text-align:center;'>" . number_format($totalCount) . "</th></tr></table>";
			
			
			
		}
		else
		{
			echo "Login, dog! <br /><a href='http://dev.floathope.com/login.php' target='_blank'>Do it</a>";
		}
	?>
	  </div>
	 </div>
	</div>
	<!-- <hr />
	<img src="http://www.drharveys.com/images/image_server/web_image/5/300.jpg" width=280 /> -->
</body>
</html>

<?php 
function refreshCookie($userid)
{
	$user = getUserFromDB($userid);
	
	// get key info
	$keyinfo = $_COOKIE['KEY_floathope'];
	
	// delete old cookie
	$past = time() - 100;
	setcookie(USER_floathope, gone, $past);
	setcookie(EMAIL_floathope, gone, $past);
	setcookie(KEY_floathope, gone, $past);
	
	// set new cookie
	$thirtyDays = time() + 60*60*24*30;
	setcookie(USER_floathope, htmlspecialchars($user->printUserJSON()), $thirtyDays);
	setcookie(EMAIL_floathope, htmlspecialchars($user->email), $thirtyDays);
	setcookie(KEY_floathope, htmlspecialchars($keyinfo), $thirtyDays);
}
?>