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

?>
<!DOCTYPE html>
<html>
<head>
	<title>Float Hope - Payment Limit Reached</title>
	
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
			<div class="user-module"> <!-- Start left column -->
			<h2>You have reached your limit for the month!</h2>
			
			<?php 
			$userCharity = $userArray->{'charityList'};
			$myCharity = $userCharity[0];
			
			// Get monthly user stats
			$monthlyStats = getMonthlyStats($userid);
			$month = date("F Y");
			
			$totalCount = 0;
			
			// calculate the total # of indulgences
			foreach($monthlyStats as $domain => $count)
			{
				$totalCount += $count;
			}
			
			// get charge information
			$chargeInfo = getChargeInfo($userid);
			foreach($chargeInfo as $limit => $charge)
			{
				$monthlyDollarLimit = $limit;
				$chargePerVisit = $charge;
			}
			$chargeThisMonth = $totalCount * $chargePerVisit;
	
			// check if exceeded limit
			if($chargeThisMonth > $monthlyDollarLimit)
			{
				// set charge to limit if exceeded & set overage
				$overage = $chargeThisMonth - $monthlyDollarLimit;
				$chargeThisMonth = $monthlyDollarLimit;
			}
			else 
			{
				$overage = 0;
			}
			
			$chargeThisMonth = number_format($chargeThisMonth, 2);
			
			echo "<p>Since you visited <strong>$totalCount</strong> indulgences, your suggested donation is <strong>\$$chargeThisMonth</strong>.</p>";
			
			if($overage > 0)
			{
				$overage = number_format($overage, 2);
				$totalSuggested = $chargeThisMonth + $overage;
				echo "<p>But for real, you accrued <strong>\$$overage</strong> over that, so you
					may want to consider donating <strong>\$$totalSuggested</strong> :)</p>";
			}
			
			echo "<h3>Charity Details</h3><hr />";
			getCharityButton($myCharity); 
			
			
			?>
			
			
		
			</div>
			</div> <!--  End hero-unit & left column -->
		
		<div class="span6"><div class="user-module"> <!-- Start right column -->
			
		</div></div> <!-- End user module & right column -->
	</div>
	
  </div>
  

</body>
</html>

<?php 
function getCharityButton($charity)
{
	switch ($charity)
	{
		case "Samasource":
			echo "<script language=\"javascript\"type=\"text/javascript\">function FG_4ccb04fb46c30_loadScript(){var fgScriptUrl='http://donate.firstgiving.com/dpa/static/js/site/fg_consumer_donation_opener.min.js';var script=document.createElement(\"script\");script.type=\"text/javascript\";if(script.readyState){script.onreadystatechange=function(){if(script.readyState==\"loaded\"||script.readyState==\"complete\"){script.onreadystatechange=null}}}else{script.onload=function(){}}script.src=fgScriptUrl;document.body.appendChild(script)}FG_4ccb04fb46c30_loadScript();</script></script> 

<!-- Basic information about this charity. You may remove this DIV if you do not need it displayed -->
<div style=\"width: 200px;\"><div id=\"fgOrganizationName\"><strong>SAMASOURCE INC</strong></div><strong>EIN:</strong>262547062</div><br />
<!-- Donation Button block -->
<div id=\"fg_donation-button-block\">
<!-- The Donation Button. You can restyle this to fit with your site design by removing the inline style elements. -->
<a href=\"javascript:void(0)\" id=\"454d8f30-2024-11e0-a279-4061860da51d\" class=\"fg-donation-button-4ccb04fb46c30\" onclick=\"FG_DONATE_BUTTON.openDonationWindow(this, 'https://donate.firstgiving.com'); return false;\" style=\"text-indent: -999999px; background: url(http://donate.firstgiving.com/dpa/static/img/consumer_donation_button.png) no-repeat; width: 202px; height: 62px; display: block; pointer: cursor; outline: 0;\">Donate Now</a>
<!-- Get this FirstGiving Donation Button block. You may remove this if it does not fit with your design. -->
<div id=\"fg_get-this-action\" style=\"width: 200px; margin-top: 5px;\">
<a href=\"javascript:void(0)\" style=\"text-decoration: none;\">
</a>
</div>
</div>";
			
			break;
			
		case "Kiva":
			echo "<script language=\"javascript\"type=\"text/javascript\">function FG_4ccb04fb46c30_loadScript(){var fgScriptUrl='http://donate.firstgiving.com/dpa/static/js/site/fg_consumer_donation_opener.min.js';var script=document.createElement(\"script\");script.type=\"text/javascript\";if(script.readyState){script.onreadystatechange=function(){if(script.readyState==\"loaded\"||script.readyState==\"complete\"){script.onreadystatechange=null}}}else{script.onload=function(){}}script.src=fgScriptUrl;document.body.appendChild(script)}FG_4ccb04fb46c30_loadScript();</script></script> 

<!-- Basic information about this charity. You may remove this DIV if you do not need it displayed -->
<div style=\"width: 300px;\"><div id=\"fgOrganizationName\"><strong>KIVA MICROFUNDS</strong></div><strong>EIN:</strong>710992446</div><br />
<!-- Donation Button block -->
<div id=\"fg_donation-button-block\">
<!-- The Donation Button. You can restyle this to fit with your site design by removing the inline style elements. -->
<a href=\"javascript:void(0)\" id=\"b8543e88-edd0-11df-ab8c-4061860da51d\" class=\"fg-donation-button-4ccb04fb46c30\" onclick=\"FG_DONATE_BUTTON.openDonationWindow(this, 'https://donate.firstgiving.com'); return false;\" style=\"text-indent: -999999px; background: url(http://donate.firstgiving.com/dpa/static/img/consumer_donation_button.png) no-repeat; width: 202px; height: 62px; display: block; pointer: cursor; outline: 0;\">Donate Now</a>
<!-- Get this FirstGiving Donation Button block. You may remove this if it does not fit with your design. -->
<div id=\"fg_get-this-action\" style=\"width: 200px; margin-top: 5px;\">
<a href=\"javascript:void(0)\" style=\"text-decoration: none;\">
</a>
</div>
</div>";
			
			break;
			
		case "Operation Smile":
			echo "<script language=\"javascript\"type=\"text/javascript\">function FG_4ccb04fb46c30_loadScript(){var fgScriptUrl='http://donate.firstgiving.com/dpa/static/js/site/fg_consumer_donation_opener.min.js';var script=document.createElement(\"script\");script.type=\"text/javascript\";if(script.readyState){script.onreadystatechange=function(){if(script.readyState==\"loaded\"||script.readyState==\"complete\"){script.onreadystatechange=null}}}else{script.onload=function(){}}script.src=fgScriptUrl;document.body.appendChild(script)}FG_4ccb04fb46c30_loadScript();</script></script> 

<!-- Basic information about this charity. You may remove this DIV if you do not need it displayed -->
<div style=\"width: 300px;\"><div id=\"fgOrganizationName\"><strong>OPERATION SMILE INC</strong></div><strong>EIN:</strong>541460147</div><br />
<!-- Donation Button block -->
<div id=\"fg_donation-button-block\">
<!-- The Donation Button. You can restyle this to fit with your site design by removing the inline style elements. -->
<a href=\"javascript:void(0)\" id=\"b966956e-edd0-11df-ab8c-4061860da51d\" class=\"fg-donation-button-4ccb04fb46c30\" onclick=\"FG_DONATE_BUTTON.openDonationWindow(this, 'https://donate.firstgiving.com'); return false;\" style=\"text-indent: -999999px; background: url(http://donate.firstgiving.com/dpa/static/img/consumer_donation_button.png) no-repeat; width: 202px; height: 62px; display: block; pointer: cursor; outline: 0;\">Donate Now</a>
<!-- Get this FirstGiving Donation Button block. You may remove this if it does not fit with your design. -->
<div id=\"fg_get-this-action\" style=\"width: 200px; margin-top: 5px;\">
<a href=\"javascript:void(0)\" style=\"text-decoration: none;\">
</a>
</div>
</div>";
			
			break;
			
		case "UNICEF":
			echo "<script language=\"javascript\"type=\"text/javascript\">function FG_4ccb04fb46c30_loadScript(){var fgScriptUrl='http://donate.firstgiving.com/dpa/static/js/site/fg_consumer_donation_opener.min.js';var script=document.createElement(\"script\");script.type=\"text/javascript\";if(script.readyState){script.onreadystatechange=function(){if(script.readyState==\"loaded\"||script.readyState==\"complete\"){script.onreadystatechange=null}}}else{script.onload=function(){}}script.src=fgScriptUrl;document.body.appendChild(script)}FG_4ccb04fb46c30_loadScript();</script></script> 

				<!-- Basic information about this charity. You may remove this DIV if you do not need it displayed -->
				<div style=\"width: 300px;\"><div id=\"fgOrganizationName\"><strong>UNITED STATES FUND FOR UNICEF</strong></div><strong>EIN:</strong>131760110</div> <br />
				<!-- Donation Button block -->
				<div id=\"fg_donation-button-block\">
				<!-- The Donation Button. You can restyle this to fit with your site design by removing the inline style elements. -->
				<a href=\"javascript:void(0)\" id=\"bbadee3a-edd0-11df-ab8c-4061860da51d\" class=\"fg-donation-button-4ccb04fb46c30\" onclick=\"FG_DONATE_BUTTON.openDonationWindow(this, 'https://donate.firstgiving.com'); return false;\" style=\"text-indent: -999999px; background: url(http://donate.firstgiving.com/dpa/static/img/consumer_donation_button.png) no-repeat; width: 202px; height: 62px; display: block; pointer: cursor; outline: 0;\">Donate Now</a>
				<!-- Get this FirstGiving Donation Button block. You may remove this if it does not fit with your design. -->
				<div id=\"fg_get-this-action\" style=\"width: 200px; margin-top: 5px;\">
				<a href=\"javascript:void(0)\" style=\"text-decoration: none;\">
				</a>
				</div>
				</div>";
			
			break;
	}
}

?>