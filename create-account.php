<?php 
// @author jack krawczyk -- @jackk
// last updated: Nov 29, 2012

// This is the user signup page. All account creation takes place within this interface.
// If a user is logged in, they will be redirected into their homepage.

	// includes for php
	include 'includes.php';
	
	if(isset($_COOKIE['EMAIL_floathope']))
	{
		header("Location: home.php");
	}
	
	// Get dynamic variables for signup
	$charities = getCharities();
	
	// get sites if post hasn't been set
	if($_POST == null)
	{
		$mySites = getTopSites_SignUp(5);
	}

?>
<!DOCTYPE html>
<html>
<head>
	<title>Float Hope - Create Account</title>
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
	
	<!-- Load Scripts -->
	<script src="jquery-1.7.1.js"></script>
	<script src="jquery-cookie.js"></script>
</head>
<body>
	<img class="homepage" src="img/background.jpg" style="z-index:-1000;" />
	
	<div class="container" style="padding-top:20px;">
	<script>
	    $(document).ready(function() {
			$("#error-bar").hide();
	    }); 
	</script>
<?php 
		if(isset($_POST['email']))
		{
			//echo var_dump($_POST);
			/* QUERY
			 *  insert into user (first_name, last_name, email, max_charge_dollars, charge_per_visit_cents, password, charityid)
			 *  VALUES ("Test", "Num1", "test@test.com", 30, .05, PASSWORD('test'),1);
			 */
			$email = $_POST['email'];
			
			$errorFlag = false;
			
			//******** VALIDATE EMAIL REGEX ****************
			// Validate if email address already exists
			// Email validation
			if($email == null)
			{
				echo "<script>$(document).ready(function() { $('#error-bar').append('<li><strong>Missing Email</strong>'); } ); </script>\n";
				$errorFlag = true;
				$errorEmail = true;
			}
			else
			{
				if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
				{
					// valid email
					$errorEmail = false;
					
					if(checkForEmail($email) > 0)
					{
						//echo "<li>There is already an account with the email: " . $email . " <a href='index.php'>Log In</a> \n";
						echo "<script>$(document).ready(function() { $('#error-bar').append('<li>There is already an account with the email: " . $email . " <a href='index.php'>Log In</a>'); } ); </script> \n";
						
						$errorFlag = true;
					}
				}
				else
				{
					// invalid email
					echo "<script>$(document).ready(function() { $('#error-bar').append('<li><strong>Invalid Email</strong>'); } ); </script>\n";
					$errorFlag = true;
					$errorEmail = true;
				}
			}
			
			
			$defaultMaxCharge = "50";
			$defaultChargePerVisit = "0.25";
			
			// get post data
			$firstName = trim($_POST['firstName']);
			$lastName = trim($_POST['lastName']);
			$charityID = $_POST['charity'];
			$password = $_POST['password'];
			$password2 = $_POST['password2'];
			
			// Get submitted sites
			$submittedSites = $_POST['sites'];
			
			// Append added sites
			$addedSites = $_POST['addedsites'];
			
			if($firstName == null)
			{
				echo "<script>$(document).ready(function() { $('#error-bar').append('<li><strong>Missing First Name</strong>'); } ); </script>\n";
				$errorFlag = true;
				$errorFN = true;
			}
			else
			{
				if(! preg_match("/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#0-9]+/", $firstName))
				{
					// valid name
					$errorFN = false;
				}
				else
				{
					// invalid name
					echo "<script>$(document).ready(function() { $('#error-bar').append('<li><strong>Invalid First Name</strong>'); } ); </script>\n";
					$errorFlag = true;
					$errorFN = true;
				}
			}
			
			// Last Name Validation
			if($lastName == null)
			{
				echo "<script>$(document).ready(function() { $('#error-bar').append('<li><strong>Missing Last Name</strong>'); } ); </script>\n";
				$errorFlag = true;
				$errorLN = true;
			}
			else
			{
				if(! preg_match("/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#0-9]+/", $lastName))
				{
					// valid name
					$errorLN = false;
				}
				else
				{
					// invalid name
					echo "<script>$(document).ready(function() { $('#error-bar').append('<li><strong>Invalid Last Name</strong>'); } ); </script>\n";
					$errorFlag = true;
					$errorLN = true;
				}
			}
			
			
			
			// Password Validation
			if($password == null)
			{
				echo "<script>$(document).ready(function() { $('#error-bar').append('<li><strong>Missing Password</strong>'); } ); </script>\n";
				$errorFlag = true;
				$errorPW = true;
			}
			else
			{
				if(! preg_match("/[\^<,\"@\/\{\}\(\)\*\$%\?=>:\|;#]+/", $password))
				{
					// make sure passwords match
					if($password == $password2)
					{
						// valid password
						$errorPW = false;
					}
					else
					{
						// invalid password
						echo "<script>$(document).ready(function() { $('#error-bar').append('<li><strong>Passwords do not match</strong>'); } ); </script>\n";
						$errorFlag = true;
						$errorPW = true;
					}
				}
				else
				{
					// invalid password
					echo "<script>$(document).ready(function() { $('#error-bar').append('<li><strong>Invalid Password (cannot contain special characters)</strong>'); } ); </script>\n";
					$errorFlag = true;
					$errorPW = true;
				}
			}
			
			// Check that at least one site was submitted
			if(count($submittedSites) == 0 && $addedSites == null)
			{
				// Notify that one site is required
				echo "<script>$(document).ready(function() { $('#error-bar').append('<li><strong>Select at least one Indulgence</strong>'); } ); </script>\n";
				$errorFlag = true;
				$errorSite = true;
			}
			
			// get Sites
			$mySites = selectedSites();
			
			// only add account if no errors
			if(! $errorFlag)
			{
				// create the user - pre sites
				$userid = addNewUser($firstName, $lastName, $email, $defaultMaxCharge, $defaultChargePerVisit, $password, $charityID);
				
				// add sites to the user
				foreach($mySites as $domain => $siteid)
				{
					addUserSiteByID($userid, $siteid);
				}
				
				// send email to new user
				$charity_name = getCharity($charityID);
				$namespace = "http://dev.floathope.com/login.php";
				$emailContent = "Dear $firstName $lastName,\n\nThank you for joining FloatHope! We are excited to help you take some of the edge off when cruising around the Internet.
				\nAll of your indulgences will go to support $charity_name. You will receive an email each month when you have hit your goal of $30.
				\nThe Internet is a great place that helps us kill some time. Welcome aboard the best way to make that time productive!
				\nBe sure to install the Chrome Extension when you log in to your account at $namespace
				
				Sincerely,
				Jack
				FloatHope Founder";
				$headers = "From: account@floathope.com \r\n Reply-To: account@floathope.com \r\n X-Mailer: PHP/" . phpversion();
				mail($email, "Welcome to FloatHope!", $emailContent, $headers);
				
				// Showcase confirmation to user
				echo "<div class='hero-unit'><h2>Welcome to FloatHope!</h2>";
				echo "<p>We're excited to have you, $firstName.</p>";
				
				// create form
				echo "<form method='post' action='login.php'> \n";
				echo "<input name='email' type='hidden' value='$email' /> \n";
				echo "<input name='password' type='hidden' value='$password' /> \n";
				echo "<input name='submit' type='submit' class=\"btn btn-success btn-large\" value='Get Started!' /> \n";
				echo "</form></div>";
				
				die;
			}
			else // errors were found
			{
				// print error bar
				echo "<script>$(document).ready(function() { $('#error-bar').prepend('<p>Please correct the following items:</p>'); $('#error-bar').show();  }); </script>";
				
				// recreate any added elements (if they exist)
				// **** need to code this out
			}
		}
	?>
	
	<div class="row">
		<div class="span12">
			<h1 style="color:#000; padding-bottom:20px;">Create Account </h1>
		</div>
	</div>
	<div class="alert alert-error" id="error-bar">
		
	</div>
	 <div class="row" style="color:#000;">
	 <form method="post" action="create-account.php" class="form-horizontal" id="main-form">
	 	<div class="span5">
	 	<div class="user-module">
	 		<h3>Basic Information</h3>
			
				<fieldset <?php if($errorFN) echo "class='control-group error' style='margin-bottom:0px;'"; ?>>
					<label for="firstName" class="signup-label">First Name:</label>
					<input id="firstName" name="firstName" type="text" value="<?php echo $firstName; ?>" <?php if($errorFN) echo "class='control-group error'"; ?> />
				</fieldset>
				<fieldset <?php if($errorLN) echo "class='control-group error' style='margin-bottom:0px;'"; ?>>
					<label for="lastName" class="signup-label">Last Name:</label>
					<input id="lastName" name="lastName" type="text" value="<?php echo $lastName; ?>" <?php if($errorLN) echo "class='control-group error'"; ?> />
				</fieldset>
				<fieldset <?php if($errorEmail) echo "class='control-group error' style='margin-bottom:0px;'"; ?>>
					<label for="email" class="signup-label">Email:</label>
					<input id="email" name="email" type="text" value="<?php echo $email; ?>" <?php if($errorEmail) echo "class='control-group error'"; ?> />
				</fieldset>
				<hr />
				<fieldset <?php if($errorPW) echo "class='control-group error' style='margin-bottom:0px;'"; ?>>
					<label for="password" class="signup-label">Password:</label>
					<input id="password" name="password" type="password" value="" <?php if($errorPW) echo "class='control-group error'"; ?> />
				</fieldset>
				<fieldset>
					<label for="password2" class="signup-label">Confirm Password:</label>
					<input id="password2" name="password2" type="password" value="" />
				</fieldset>
		</div></div>
		<div class="span5">
		<div class="user-module">
				<fieldset>
					<h3>My Indulgence Sites:</h3>
					<script>
				    
					$(document).ready(function(){
						 
					    var i = 6;

					    $('#addsite').click(function() {
					        $('<div class="remove-inputbox" id="remove' + i + '" style="padding-bottom:3px;"><input id="site' + i + '" name="addedsites[]" type="text" value="www." class="input-xlarge" /> <a href="#" class="remove-link" id="remove' + i + '"> <i class="icon-remove" id="site' + i + '" style="margin-top:-4px;" /></div>').fadeIn('100').appendTo('#inputs-sites');
					        //$('<div class="remove-inputbox" id="remove' + i + '"><input id="site' + i + '" name="addedsites[]" type="text" value="www." class="input-xlarge" /> <a href="#" class="remove-link" id="remove' + i + '"> <i class="icon-remove" id="site' + i + '" style="margin-top:-4px;" /></div>').appendTo('form');
					        //$('#remove' + i).fadeIn('100').appendTo('#inputs-sites');
					        i++;
					    });

					    // since the links are added dynamically, we need to create a binding element using on to make the app listen
					    // .on() is the key to getting these text boxes removed
					    $('body').on("click", "a.remove-link", function(){
							var parentid = $(event.target).parent();
							removeid = "#" + parentid.attr('id');

						    $(removeid).remove();

						    //don't decrement to avoid duplicates
						    //i--;
						});

					    // here's our click function for when the forms submitted
					 	// need this to update the fields
					    
					    $('body').on("click", "#submit", function(){
					    	var answers = [];
					        $.each($('.input-xlarge'), function() {
					            answers.push($(this).val());
					        });

				     		//alert(answers);	

				     			
					    });
					 
					});
					</script>
					<div  class="control-group">
						<div id='inputs-sites' class="controls">
					<?php 
						$counter_site = 0;
						foreach($mySites as $site_domain => $siteid)
						{
							echo "<label class=\"checkbox\">\n";
							
							echo "  <input id='site$counter_site' name='sites[]' type='checkbox' value='$siteid' checked />$site_domain <br />\n";
							
							echo "</label>\n";
							
							$counter_site++;
						}
					?>
						<!-- <label class="checkbox">
						 <input id="site1" name="sites[]" type="checkbox" value="www.facebook.com" class="input-xlarge"  checked />Facebook <br />
						</label>
						<label class="checkbox">
						 <input id="site2" name="sites[]" type="checkbox" value="twitter.com" class="input-xlarge" checked />Twitter <br />
						</label>
						<label class="checkbox">
						 <input id="site3" name="sites[]" type="checkbox" value="www.stumbleupon.com" class="input-xlarge" checked />StumbleUpon <br />
						</label>
						<label class="checkbox">
						 <input id="site4" name="sites[]" type="checkbox" value="jezebel.com" class="input-xlarge" checked />Jezebel <br />
						</label>
						<label class="checkbox">
						 <input id="site5" name="sites[]" type="checkbox" value="www.youtube.com" class="input-xlarge" checked />YouTube <br />
						</label> -->
						</div>
					</div>
					<label for="addMore"><a href="#" id="addsite">add more...</a></label>
					
				</fieldset>
				<hr />
				<fieldset>
					<h3>My Charity:</h3>
					<div  class="control-group">
					<div id='inputs-charity' class="controls">
					<?php 
						$counter_charity = 0;
						foreach($charities as $cid => $charity_name)
						{
							echo "<label class=\"radio\">";
							
							if($counter_charity==0)
								echo "  <input id='charity$counter_charity' name='charity' type='radio' value='$cid' checked />$charity_name <br />\n";
							else 
								echo "  <input id='charity$counter_charity' name='charity' type='radio' value='$cid' />$charity_name <br />\n";
						
							
							echo "</label>\n";
							
							$counter_charity++;
						}
						
						
					?>
					</div>
					</div>
					<!-- <input id="charity1" name="charity" type="radio" value="1" checked/>Samasource <br />
					<input id="charity2" name="charity" type="radio" value="3" />Operation Smile <br />
					<input id="charity3" name="charity" type="radio" value="4" />UNICEF <br /> -->
				</fieldset>
				<hr />
				<fieldset>
					<input id="submit" name="submit" type="submit" class="btn btn-success btn-large"  value="Sign Up" />
				</fieldset>
			
		</div></div>
		<div class="span2">
		<div class="user-module">
		<p>Have an account?</p>
		<p><a href="login.php">Log in</a></p>
		</div></div>
	 </form>
	 </div>
	</div>
</body>
</html>
<?php 
	function selectedSites()
	{
		if($_POST != null)
		{
			$selectedSites = array();
			
			// Get submitted sites
			$submittedSites = $_POST['sites'];
			
			// Append added sites
			$addedSites = $_POST['addedsites'];
			
			// add back selected sites
			if($submittedSites != null)
			{
				foreach($submittedSites as $siteid)
				{
					$getDomain = getSiteDomain($siteid);
					$selectedSites[$getDomain] = $siteid;
				}
			}
			
			// add newly submitted sites
			if($addedSites != null)
			{
				foreach($addedSites as $newDomain)
				{
					if(preg_match('|^[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $newDomain))
					{
						//echo "<script>alert(\"$newDomain is a valid site.\");</script>";
						// valid site domain -> process
						$getSiteID = getSiteID($newDomain);
						
						// if site doesn't exist, add it
						if($getSiteID == "No record")
						{
							// add the site
							$getSiteID = addNewDomain($newDomain);
						}
						
						// add site to the selected array
						$selectedSites[$newDomain] = $getSiteID;
						
					}
					else
					{
						//echo "<script>alert(\"$newDomain is NOT a valid site.\");</script>";
						// not a valid site -- alert user
						
						echo "<script>$(document).ready(function() { $('#error-bar').append('<li><strong>$newDomain is not a valid domain name</strong>'); } ); </script>\n";	
					}
				}
			}
			
			return $selectedSites;
		}
		else 
		{
			return null;
		}
		
	}
?>