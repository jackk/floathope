<?php 
	// includes for php
	include 'includes.php';
	
	// reroute if not logged in
	if(! isset($_COOKIE['EMAIL_floathope']))
	{
		header("Location: index.php");
	}

	// parse out userid from userCookie
	$userArray = json_decode(htmlspecialchars_decode($_COOKIE['USER_floathope']));
	//get name information
	$userid = $userArray->{'userid'};
	$name = $userArray->{'name'};
	
	// process any updates if posted from edit
	if(isset($_POST['flag']))
	{
		$success = processUpdate($_POST['flag'], $userid);
		
		if($success)
		{
			// parse out userid from userCookie
			$userArray = json_decode(htmlspecialchars_decode($_COOKIE['USER_floathope']));
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Float Hope - My Profile</title>
	
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
              <li><a href="profile.php">Logged in as <strong><?php echo $name; ?></strong></a></li>
              <li class="divider-vertical"></li>
              <li><a href="logout.php">Log out</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>
	
	<div class="container">
		<div class="row">
	<?php 
		// parse out userid from userCookie
		//$userArray = json_decode(htmlspecialchars_decode($_COOKIE['USER_floathope']));
		
		$userid = $userArray->{'userid'};
		
		//var_dump($userArray);
		
		// get profile info
		$profileInfo = getProfileInfo($userid);
		$chargePer = number_format($profileInfo[1],2);
		$monthlyLimit = $profileInfo[2];
	?>
		</div>
	
		<div class="row">
			<div class="span6">
			<div class="user-module">
	<?php 
		// ***** PROFILE INFO *****
		
		// highlight any errors
		if(isset($_POST['flag']))
		{
			$type = $_POST['flag'];
			switch($type)
			{
				case "charity":
					$type = "charity information";
					break;
				case "payment":
					$type = "indulgence charges";
					break;
				case "password":
					$type = "password";
					break;
			}
			// successful update
			if($success)
			{
				echo "<div class='alert alert-success'>";
				echo "Your <strong>$type</strong> has been successfully updated.";
			}
			else // not successful update
			{
				echo "<div class='alert alert-error'>";
				echo "There was an error updating your <strong>$type</strong>. Please try again.";
			}
			
			echo "</div>";
		}
		
		// get user profile info
		
		echo "<table class='table table-striped'>
			<tbody><tr><td colspan='2'><h3>My Account Info</h3></td></tr>";
		
		// Charity Row
		// cookie doesn't update in real time -- use this to update it
		if(isset($_POST['charity']))
		{
			$charity = getCharity($_POST['charity']);
		}
		else 
		{
			$charity = $userArray->{'charityList'};
			$charity = $charity[0];
		}
		echo "<tr><td><strong>Charity:</strong> </td><td>$charity<br /> <em><a href='edit.php?type=charity'>(edit)</a></em></td></tr> \n";
		
		// Charge Info
		if(isset($_POST['inputChargePer']) && isset($_POST['inputMonthlyLimit']) && $success)
		{
			// successful update of payment - set new values
			$chargePer = number_format($_POST['inputChargePer'],2);
			$monthlyLimit = $_POST['inputMonthlyLimit'];
		}
		echo "<tr><td><strong>Charge per indulgence:</strong></td><td>\$$chargePer <br /><em><a href='edit.php?type=payment'>(edit)</a></em></td></tr> \n";
		echo "<tr><td><strong>Monthly limit:</strong> </td><td>\$$monthlyLimit <br /> <em><a href='edit.php?type=payment'>(edit)</a></em></td></tr> \n";
		
		// Billing Row
		echo "<tr><td><strong>Password:</strong></td>
			<td>xxxxxxx
			<br /> <em><a href='edit.php?type=password'>(edit)</a></em></td>";
	
		echo "</tbody>\n</table>";
		
		// ****** TRANSACTIONS ******
		
		echo "<table class='table table-striped'>
			<tbody><tr><td colspan='2'><h3>My Donations</h3></td></tr>";
		
		echo "<tr><td>You have not yet registered a donation.</td></tr> \n";
		
		echo "</tbody>\n</table>";
		
		?>
		
		</div></div> <!--  End hero-unit & left column -->
		
		<div class="span6"><div class="user-module"> <!-- Start right column -->
		<?php 
		/*
		// Get sites
		$mysites = $userArray->{"guiltySites"};
		
		echo "<table class='table table-striped'>
			<tbody><tr><td><h3>My Indulgences</h3></td>
			<td style='text-align:right'><em><a href='edit.php?type=indulgences'>(edit)</a></em></td></tr>";
		
		foreach($mysites as $mysite)
		{
			echo "<tr><td><a href='site.php?ind=$mysite'>" . $mysite . "</a></td>";
			echo "<td>
					<form action='follow-site.php' method='post' id='removeit'> 
							<input type='submit' class='btn btn-small' style='width:80px;' value='Remove it' />  
					</form>
				  </td></tr>";
		}
		
		echo "</tbody>\n</table>";
		
		
		*/
		?>
		</div></div> <!-- End user module & right column -->
	</div>
	
  </div>
  

</body>
</html>

<?php 
function processUpdate($flag, $userid)
{
	switch ($flag)
	{
		// ********************************
		// Update Charity selected by user
		// ********************************
		case "charity":
			$charityid = $_POST['charity'];
			
			// update Charity info in database
			updateCharity($userid, $charityid);
			
			// update user cookie to reflect new charity
			$user = getUserFromDB($userid);
			$past = time() - 100;
			setcookie(USER_floathope, gone, $past);
			$thirtyDays = time() + 60*60*24*30;
			setcookie(USER_floathope, htmlspecialchars($user->printUserJSON()), $thirtyDays);
			
			return true;
			
			break;
		
		// ********************************
		// Update Payment selected by user
		// ********************************
		case "payment":
			$chargePer = $_POST['inputChargePer'];
			$monthlyLimit = $_POST['inputMonthlyLimit'];
			
			$chargePer = number_format($chargePer, 2);
			
			// validate input
			// monthly limit - must be an integer
			if(preg_match('/^\d+$/', $monthlyLimit))
			{
				// monthly limit is valid
				$limitError = false;
			}
			else
			{
				// monthly limit is not a valid integer
				$limitError = true;
			}
			
			// charge per must be a float
			if(preg_match('/^\d+\.?\d*$/', $chargePer))
			{
				// chargePer is valid
				$chargePerError = false;
			}
			else
			{
				// chargePer is invalid
				$chargePerError = true;
			}
			
			// if there are no errors, update the data
			if(! $limitError && ! $chargePerError)
			{
				updatePaymentInfo($userid, $chargePer, $monthlyLimit);
				
				return true;
			}
			else 
			{
				return false;
			}
			
			break;
			
		case "password":
			// Password Validation
			$currentPassword = $_POST['currentPass'];
			$pass1 = $_POST['newPass1'];
			$pass2 = $_POST['newPass2'];
			
			if($currentPassword == "" || $pass1 == "" || $pass2 == "")
			{
				// make sure there were inputs for each
				return false;
			}
			else
			{
				// check current password is correct
				if(! preg_match("/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/", $currentPassword))
				{
					// password is correct - make sure that it is the right current password
					if (! getLogin($_COOKIE['EMAIL_floathope'], $currentPassword))
					{
						return false;
					}
					
					// if password is correct, validate new password
					if(! preg_match("/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/", $pass1))
					{
						// make sure passwords match
						if ($pass1 == $pass2)
						{
							// update new password
							updatePassword($userid, $pass1);
						}
						else
						{
							// passwords don't match
							return false;
						}
					}
				}
				else
				{
					// invalid password regex -- fail
					return false;
				}
			}
			
			break;
	}
}

?>