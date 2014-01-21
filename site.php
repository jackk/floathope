<?php 
// @author jack krawczyk -- @jackk
// last updated: Nov 28, 2012

// This is the profile page for sites within floathope.

	// includes for php
	include 'includes.php';
	
	// reroute if not logged in
	if(! isset($_COOKIE['EMAIL_floathope']))
	{
		header("Location: index.php");
	}
	
	if(isset($_GET['ind']))
	{
		$domain = $_GET['ind'];
	}
	else
	{
		$domain = null;
	}
	
	// parse out userid from userCookie
	$userArray = json_decode(htmlspecialchars_decode($_COOKIE['USER_floathope']));
	//get name information
	$userid = $userArray->{'userid'};
	$name = $userArray->{'name'};
	$email = $userArray->{'email'};

?>
<!DOCTYPE html>
<html>
<head prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb# floathope: http://ogp.me/ns/fb/floathope#">
	<title>Float Hope - Indulging <?php if($domain != null) echo "in $domain"; ?></title>
	<meta property="fb:app_id" content="254766557960545" /> 
	<meta property="og:type"   content="floathope:web_site" /> 
	<meta property="og:url"    content="http://dev.floathope.com/site.php?ind=<?php echo $domain; ?>" /> 
	<meta property="og:title"  content="Indulging in <?php echo $domain; ?>" /> 
	<meta property="og:image"  content="http://dev.floathope.com/dove.png" /> 
	
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
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=195408600527486";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

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
              <li class="active"><a href="site.php">Indulgences</a></li>
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
		// load the account infomation using the userid from the login cookie
		//$thisUser = $_COOKIE['USER_floathope'];
		
		//echo "Hello, here is your user info: <br />" . html_entity_decode($thisUser);
	
		// parse out userid from userCookie
		$userArray = json_decode(htmlspecialchars_decode($_COOKIE['USER_floathope']));
		$userid = $userArray->{'userid'};
	?>
		</div>
	
		<div class="row">
			<div class="span6">
			<div class="user-module">
	<?php 
		// Create Conversation Rail
		if($domain != null)
		{
			echo "<h3>Any good finds on this site?</h3><br />
				<div class='fb-comments' data-href='http://dev.floathope.com/site.php?ind=$domain' data-num-posts='4' data-width='510'></div>";
		}
		else 
		{
			// Get monthly user stats
			$monthlyStats = getMonthlyStats($userid);
			$month = date("F");
			echo "<h3>Your Current Indulgences</h3> \n<table class='table table-striped'>
				<thead>
					<tr>
						<th>Indulgence</th>
						<th style='text-align:center;'>Total Followers</th>
					</tr>
				</thead>
				<tbody>";
			
			$mySites = $userArray->{"guiltySites"};
			foreach($mySites as $mysite)
			{
				echo "<tr><td><a href='site.php?ind=$mysite'>" . $mysite . "</a></td><td style='text-align:center;'>" . getSiteFollowers($mysite) . "</td></tr>";
			}
			
			echo "</tbody></table>";
			
		}
		?>
		</div></div> <!--  End hero-unit & left column -->
		
		<div class="span6"><div class="user-module"> <!-- Start right column -->
		<?php 
		
		// show progress bar if specific site
		if ($domain != null)
		{
			// ***** INDULGENCE INFO *****
		
			echo "<h3>Indulging in $domain</h3> \n";
			
			echo "<h4>Total Followers: <span id='num-site-followers'>" . getSiteFollowers($domain) . "</span></h4>";
			
			// check if user follows this site
			$sites = $userArray->{'guiltySites'};
			$indulgeFlag = false;
			foreach($sites as $site)
			{
				if($site == $domain)
				{
					$indulgeFlag = true;
				}
			}
			
			echo "<div id='indulgence-action'>";
			
			if($indulgeFlag)
			{
				echo "<div id='following'>
						<p>You follow this site.</p> 
						<form action='follow-site.php' method='post' id='removeit'> 
							<input type='submit' class='btn btn-small' style='width:80px;' value='Remove it' />  
						</form>
						</div>";
			}
			else
			{
				echo "<div id='not-following'>
					<p>You do not indulge in this site.</p>
					<form action='follow-site.php' method='post' id='addit'>
					<input type='submit' class='btn btn-primary btn-small' style='width:80px;' value='Add it!' />  
					</form>
					</div>";
			}
			
			echo "</div>";
	?>
		<script>
		$(document).ready(function() {
			$('#addit').submit(function(event) {
				// stop the event handler
				event.preventDefault();

				var url = $(this).attr('action');

				$.post( url, { type: "follow", domain: "<?php echo $_GET['ind']; ?>" }, 
						function(data) {
					//alert("Data loaded: " + data );
					$('#indulgence-action').html("<p>You now follow this site.</p> <form action='follow-site.php' method='post' id='removeit'> <input type='submit' class='btn btn-small' style='width:80px;' disabled='disabled' value='added!' />  </form>");

					// update the number of followers
					var numFollowers = $('#num-site-followers').html();
					numFollowers++;
					$('#num-site-followers').html(numFollowers);
					 });
			});

			$('#removeit').submit(function(event) {
				// stop the event handler
				event.preventDefault();

				var url = $(this).attr('action');

				$.post( url, { type: "unfollow", domain: "<?php echo $_GET['ind']; ?>" }, 
						function(data) {
					//alert("Data loaded: " + data );
					$('#indulgence-action').html("<p>You no longer indulge in this site.</p><form action='follow-site.php' method='post' id='addit'> <input type='submit' class='btn btn-small' disabled='disabled' style='width:80px;' value='removed' />  </form>");

					// update the number of followers
					var numFollowers = $('#num-site-followers').html();
					numFollowers = numFollowers - 1;
					$('#num-site-followers').html(numFollowers);
					 });
			});
		});
		</script>
	
	<?php 		
			echo "<hr />";
			
			// Progress Bar
			// calculate impact
			$chargePerVisit = 0.25;
			$siteid = getSiteID($domain);
			$siteraised = getMonthlySiteRaised($siteid);
			$totalraise = getTotalMonthlyRaised();
			
			// check impact
			$impact = $siteraised / $totalraise * 100;
			$impactPercent = number_format($impact, 0) . "%";
			
			// print progress bar
			echo "<h3>Indulgence Impact</h3>
				<div class='progress'> <div class='bar' style='width: $impactPercent'></div> </div>
				<p><strong>$domain</strong> has raised <strong>$impactPercent</strong> of all funds this month!</p>";
		}
		else // not a specific site
		{
			echo "<h3>Tempted by the Fruit of Another</h3> <br />
			
				<p>Check out the most popular Indulgences:</p>";
			
			echo "<table class='table table-striped'>
				<thead>
					<tr>
						<th>Indulgence</th>
						<th style='text-align:center;'>Total Followers</th>
					</tr>
				</thead>
				<tbody>";
			
			$topSites = getTopSites(5);
			
			foreach($topSites as $topSite => $topFollowers)
			{
				echo "<tr><td><a href='site.php?ind=$topSite'>" . $topSite . "</a></td><td style='text-align:center;'>" . $topFollowers . "</td></tr>";
			}
			
			echo "</tbody></table>";
			
			// print search bar
			echo '<h3 >Looking for something specific?</h3> <br />
				  
				  <form class="form-search" action="search-sites.php" method="post" id="search">
					<input type="text" class="input-medium search-query" id="query">
					<button type="submit" class="btn">Search</button>
				  </form>';
			
			echo "<div id='query-results'></div>";
			
			echo "<script>$('#search').submit(function(event) {
				// stop the event handler
				event.preventDefault();

				var url = $(this).attr('action');
				var queryVal = $('#query').val();

				$.post( url, { query: queryVal }, 
						function(data) {
					//alert('Data loaded: ' + data );
					$('#query-results').html(data);
					 });
			});</script>";
		}
	
		
	?>
		</div></div> <!-- End user module & right column -->
	</div>
	
  </div>
  

</body>
</html>