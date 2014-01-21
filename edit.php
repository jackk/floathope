<?php 
// @author jack krawczyk -- @jackk
// last updated: Nov 28, 2012

// This interface is where a user goes to edit their account information:
//  Charity, password, charge per indulgence, monthly limit
// The edit interface is dictated via GET data passing.

	// includes for php
	include 'includes.php';
	
	// reroute if not logged in
	if(! isset($_COOKIE['EMAIL_floathope']))
	{
		header("Location: index.php");
	}
	
	// reroute if edit type not selected
	if(! isset($_GET['type']))
	{
		header("Location: profile.php");
	}
	
	// parse out userid from userCookie
	$userArray = json_decode(htmlspecialchars_decode($_COOKIE['USER_floathope']));
	//get name information
	$userid = $userArray->{'userid'};

?>
<!DOCTYPE html>
<html>
<head>
	<title>Float Hope - Home</title>
	
	<link rel="shortcut icon" href="dove.png" type="image/x-icon">
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
	<style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    
    <!-- Load Scripts -->
	<script src="jquery-1.7.1.js"></script>
	<script src="jquery-cookie.js"></script>
	
	<?php getGoogleAnalytics(); ?>
</head>
<body>

	<div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </a>
          <a class="brand" href="index.php">float hope</a>
          <div class="nav-collapse">
            <ul class="nav">
              <li><a href="index.php">Home</a></li>
              
              <li><a href="site.php">Indulgences</a></li>
              <li class="active"><a href="profile.php">My Profile</a></li>
            </ul>
            <ul class="nav pull-right">
              
              <li><a href="logout.php">Log out</a>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
	
	<div class="container">
	
		<div class="row">
			<div class="span6">
			<div class="user-module">
<?php 

switch($_GET['type'])
{
	// ***************************
	// Edit user charity
	// ***************************
	case "charity":
		// get charities
		$charities = getCharities();
		
		// get current charity
		$userCharity = $userArray->{'charityList'};
		$myCharity = $userCharity[0];
		
		echo "<h2>Edit Your Featured Charity</h2>";
		echo "<p>Select the charity that will benefit from your indulgences.</p>";
		
		echo "<form class='form-horizontal' method='POST' action='profile.php'>";
		echo '<div  class="control-group">';
		echo "<div id='inputs-charity' class='controls'>";
		 
		$counter_charity = 0;
		foreach($charities as $cid => $charity_name)
		{
			echo "<label class=\"radio\">";
			
			if($charity_name == $myCharity)
				echo "  <input id='charity$counter_charity' name='charity' type='radio' value='$cid' checked />$charity_name <br />\n";
			else 
				echo "  <input id='charity$counter_charity' name='charity' type='radio' value='$cid' />$charity_name <br />\n";
		
			
			echo "</label>\n";
			
			$counter_charity++;
		}
		
		echo "</div></div>";
		
		// Charity Flag
		echo "<input type='hidden' name='flag' value='charity'>";
		
		// Submit button
		echo "<div class='control-group' style='padding-top: 10px;'>
				<div class='controls'>
					<input type='submit' id='submit' value='Save' class='btn btn-primary btn-large' />
				</div>
			  </div>";
		
		echo "</form>";
		
		break;

	// ***************************
	// Edit Indulgence Limits
	// ***************************
	case "payment":
		echo "<h2>Edit Your Indulgence Limits</h2>";
		echo "<form class='form-horizontal' method='POST' action='profile.php'>";
		
		// get charge information
		$chargeInfo = getChargeInfo($userid);
		foreach($chargeInfo as $limit => $charge)
		{
			$monthlyDollarLimit = $limit;
			$chargePerVisit = number_format($charge, 2);
		}
		
		// Charge Per Indulgence
		echo "<div class='control-group' style='padding-top: 10px;'>
				<label class='control-label' for='inputChargePer' style='padding-right:20px;'>Charge per Indulgence:</label>
				<div class='controls'>
					$ <input type='text' name='inputChargePer' value='$chargePerVisit' style='width:50px;' />
				</div>
			  </div>";
		
		// Monthly Limit
		echo "<div class='control-group' style='padding-top: 10px;'>
				<label class='control-label' for='inputMonthlyLimit' style='padding-right:20px;'>Monthly Limit:</label>
				<div class='controls'>
					$ <input type='text' name='inputMonthlyLimit' value='$monthlyDollarLimit' style='width:50px;' />
				</div>
			  </div>";
		
		// Payment Flag
		echo "<input type='hidden' name='flag' value='payment'>";
		
		// Submit button
		echo "<div class='control-group' style='padding-top: 10px;'>
				<div class='controls'>
					<input type='submit' id='submit' value='Save' class='btn btn-primary btn-large' />
				</div>
			  </div>";
		
		echo "</form>";
		
		break;
	
	// ***************************
	// Password Information
	// ***************************
	case "password":
		echo "<h2>Change Your Password</h2>";
		echo "<form class='form-horizontal' method='POST' action='profile.php'>";
		
		// Current password
		echo "<div class='control-group' style='padding-top: 10px;'>
				<label class='control-label' for='currentPass' style='padding-right:20px;'>Current Password:</label>
				<div class='controls'>
					<input type='password' name='currentPass' style='width:125px;' />
				</div>
			  </div>";
		
		// New password
		echo "<hr><div class='control-group' style='padding-top: 10px;'>
				<label class='control-label' for='newPass1' style='padding-right:20px;'>New Password:</label>
				<div class='controls'>
					<input type='password' name='newPass1' style='width:125px;' />
				</div>
			  </div>";
		
		// Confirm password
		echo "<div class='control-group' style='padding-top: 10px;'>
				<label class='control-label' for='newPass2' style='padding-right:20px;'>&nbsp;</label>
				<div class='controls'>
					<input type='password' name='newPass2' style='width:125px;' />
				</div>
			  </div>";
		
		// Payment Flag
		echo "<input type='hidden' name='flag' value='password'>";
		
		// Submit button
		echo "<div class='control-group' style='padding-top: 10px;'>
				<div class='controls'>
					<input type='submit' id='submit' value='Save' class='btn btn-primary btn-large' />
				</div>
			  </div>";
		
		echo "</form>";
		
		break;	
}

?>
		
		</div></div> <!--  End hero-unit & left column -->
		
		<div class="span6"><div class="user-module"> <!-- Start right column -->
			<p></p>
		</div></div> <!-- End user module & right column -->
	</div>
	
  </div>
  

</body>
</html>