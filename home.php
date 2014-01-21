<?php 
// @author jack krawczyk -- @jackk
// last updated: Nov 28, 2012

// This is the logged in homepage for floathope. It should be the 
//  immediate destination as soon as a user is logged in, regardless
//  of access point.

	// includes for php
	include 'includes.php';
	
	// reroute if not logged in
	if(! isset($_COOKIE['EMAIL_floathope']))
	{
		header("Location: index.php");
	}

	// parse out userid from userCookie
	$userArray = json_decode(htmlspecialchars_decode($_COOKIE['USER_floathope']));
	
	$userid = $userArray->{'userid'};
	$name = $userArray->{'name'};
?>
<!DOCTYPE html>
<html>
<head>
	<title>Float Hope - Home</title>
	
	<link rel="shortcut icon" href="dove.png" type="image/x-icon">
	<link href="css/bootstrap.css" rel="stylesheet">
	<link href="css/bootstrap-responsive.css" rel="stylesheet">
	<link rel="chrome-webstore-item" href="https://chrome.google.com/webstore/detail/pmmiigcefdbcfnidmbhjmbobakfhhghc">
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
              <li class="active"><a href="index.php">Home</a></li>
              <li><a href="site.php">Indulgences</a></li>
              <li><a href="profile.php">My Profile</a></li>
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
		
		// check whether user is in Chrome
		// If no -- let them know floathope only works as a chrome extension
		// If yes -- validate whether the user has floathope installed
		echo "<script> var ua = $.browser;
				if(ua.chrome)
				{
					//document.write('Chrome');
					
					var isInstalled=false;
					
					var detect = function(base, if_installed, if_not_installed) {
					    var s = document.createElement('script');
					    s.onerror = if_not_installed;
					    s.onload = if_installed;
					    document.body.appendChild(s);
					    s.src = base + '/manifest.json';
					}
					detect('chrome-extension://pmmiigcefdbcfnidmbhjmbobakfhhghc', function() {
						$('div.browser-unit').hide();
					}, function() { $('div.checkInstall').html(\" <h1>Get FloatHope rolling with the Chrome Extension</h1> <a class='btn btn-primary btn-large' href='https://chrome.google.com/webstore/detail/pmmiigcefdbcfnidmbhjmbobakfhhghc'>Install it here</a>\");
						$('div.checkInstall').html(' <h1>Get FloatHope rolling with the Chrome Extension</h1> <button class=\"btn btn-primary btn-large\" onclick=\"chrome.webstore.install()\" id=\"install-button\">Install FloatHope to Chrome!</button>'); });
					
				}
				else
				{
					//document.write('Not Chrome');
				}
			</script>";

		echo "<div class='browser-unit'>";
		echo "<div class='checkInstall'><p style='padding-bottom:15px;'>FloatHope is currently available only for Google Chrome browsers :( </p> <a class=\"btn btn-primary btn-large\" href='http://www.google.com/chrome' target='_blank'>Download Chrome</a></div>";
		echo "</div></div>"
	?>
		
	
		<div class="row">
			<div class="span6">
			<div class="user-module">
			<h2 style='padding-bottom:15px;'>News Feed</h2>
			
			<?php getContent(); ?>
		
		</div></div> <!--  End hero-unit & left column -->
		
		<div class="span6"><div class="user-module"> <!-- Start right column -->
		<?php 
		// Get monthly user stats
		$monthlyStats = getMonthlyStats($userid);
		$month = date("F Y");
		echo "<h2>$month Stats</h2> \n<table class='table table-striped'>
			<thead>
				<tr>
					<th>Indulgence</th>
					<th style='text-align:center;'>Visits this Month</th>
				</tr>
			</thead>
			<tbody>";
		
		$totalCount = 0;
		// sort monthly stats (descending)
		arsort($monthlyStats);
		
		// list the sites with data
		foreach($monthlyStats as $domain => $count)
		{
			echo "<tr><td><a href='site.php?ind=$domain'>" . $domain . "</a></td><td style='text-align:center;'>" . number_format($count) . "</td></tr>";
			$totalCount += $count;
		}
		
		// fill in zeros
		$allMySites = $userArray->{"guiltySites"};
		foreach($allMySites as $domain)
		{
			if(! array_key_exists($domain, $monthlyStats))
			{
				echo "<tr><td><a href='site.php?ind=$domain'>" . $domain . "</a></td><td style='text-align:center;'>" . 0 . "</td></tr>";
			}
		}
		
		echo "</tbody>\n<thead><tr><th>Total</th><th style='text-align:center;'>" . number_format($totalCount) . "</th></tr></thead></table>";
		
		
		// ********** Progress Bar ***********
		// calculate progress
		
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
		
		$progress = $chargeThisMonth/$monthlyDollarLimit * 100;
		$progressPercent = number_format($progress, 0) . "%";
		
		$charity = $userArray->{'charityList'};
		
		// format dollars
		$chargeThisMonth = number_format($chargeThisMonth, 2);
		$monthlyDollarLimit = number_format($monthlyDollarLimit, 2);
		$overage = number_format($overage, 2);
		
		// print progress bar
		echo "<h2 style='padding-bottom:10px;'>My Monthly Impact</h2>
			<div class='progress'> <div class='bar' style='width: $progressPercent'></div> </div>
			<p><strong>\$$chargeThisMonth</strong> toward your goal of <strong>\$$monthlyDollarLimit</strong> for <strong>$charity[0]</strong></p>";
		
		// highlight overage if exists
		if($overage > 0)
		{
			echo "<p><strong>Naughty, naughty!</strong> You are \$$overage over your goal this month.</p>"; 
		}
		
	?>
		</div></div> <!-- End user module & right column -->
	</div>
	
  </div>
</div>  

</body>
</html>

<?php 

// this method loads the content portion of the homepage
// any RSS feed can be included in the $doc->load line
function getContent()
{
	$doc = new DOMDocument();
	$doc->load('http://news.google.com/?output=rss');
	$arrFeeds = array();
	foreach ($doc->getElementsByTagName('item') as $node) 
	{
	    $itemRSS = array ( 
	      'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
	      'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
	      'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
	      'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
	      );
	    array_push($arrFeeds, $itemRSS);
	    
	    $title = $itemRSS['title'];
	    $link = $itemRSS['link'];
	    $description = $itemRSS['desc'];
	    $date = $itemRSS['date'];
	    
	    echo "<h4 style='padding-bottom:10px;'><a href='$link' target='_blank'>$title</a></h4>";
	}
	echo "<h4 style='padding-bottom:10px;'><a href='http://www.facebook.com/' target='_blank'>Check my Facebook</a></h4>";
	echo "<h4 style='padding-bottom:10px;'><a href='http://twitter.com/' target='_blank'>Check Twitter</a></h4>";
}

?>
