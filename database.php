<?php
// @author jack krawczyk -- @jackk
// last updated: Nov 28, 2012

// FloatHope was built on a MYSQL database. These classes assume a standard implementation.
// The code below is defaulted into your localhost.

function connectDB()
{
	// localhost database example
	// fill in with your hosted database instance
	$hostname="127.0.0.1";
	$database="floathope";
	$username="root";
	$password="";
	
	// establish connection to database
	$mysqli = new mysqli($hostname, $username, $password, $database);
	
	if ($mysqli->connect_error) {
	die('Connect error(' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
	}
	else{
	    // uncomment for any debugging 
		// echo "Connection to MySQL server " . $mysqli->hostname . " successful!" . PHP_EOL;
	}
	
	return $mysqli;
}

// Close database. Always close objects that call for a database connection.
// Parameter type: mysqli
function closeDB($mysqli)
{
	$mysqli->close();
}

// Pull relevant user information by floathope userid
// Parameter type: integer
function getUserFromDB($userid)
{
	$mysqli = connectDB();
	
	// inherits user class from user.php (included when called in includes.php)
	$user = new user($userid);
	
	// Construct user name, email, & charity query & pull data 
	$getUser = "SELECT first_name, last_name, email, c.name FROM user u JOIN charity c ON u.charityid=c.charityid WHERE userid=" . $userid . ";";
	
	if($result = $mysqli->query($getUser))
	{
		if ($result->num_rows == 0)
			return null; // return null if no user matches the id
			
		while($row = $result->fetch_row())
		{
			$user->setFirstName($row[0]);
			$user->setLastName($row[1]);
			$user->setEmail($row[2]);
			$user->addCharity($row[3]);
		}
		
		$result->close();
	}
	
	// After name & charity are selected, pull the sites that the user is tracking.
	$getSites = "SELECT domain FROM site s JOIN usersites us ON s.siteid=us.siteid WHERE userid=" . $userid . ";";
	
	if($result = $mysqli->query($getSites))
	{
		while($row = $result->fetch_row())
		{
			$user->addGuiltySite($row[0]);
		}
		
		$result->close();
	}
	
	closeDB($mysqli);
	
	return $user;
}

// This method returns the count of visits to each guilty pleasure site in the current month.
// Parameter type: integer
// Return type: associative array (domain->count)
// Thoughts for a future implementation: a second (optional) parameter, selecting month/year to 
//  look back beyond the current month.
function getMonthlyStats($userid)
{
	$mysqli = connectDB();
	
	// Monthly Stats query
	$getStats = "select domain, count(domain) from recordedvisit r inner join site s on r.siteid=s.siteid where userid=" . $userid . " and id>concat(year(now()), '-', month(now()), '-01 0:0:0') group by domain order by count(domain) DESC;";
	
	$monthlyStats = array();
	
	if($result = $mysqli->query($getStats))
	{
		while($row = $result->fetch_row())
		{
			$domain = $row[0];
			$count = $row[1];
			
			$monthlyStats[$domain] = $count;
		}
	}
	
	
	closeDB($mysqli);
	
	return $monthlyStats;
}

// When viewing the statistics for a site, return the total amount of visits to that site 
//  globally in the current month.
// Parameter type: integer
// Return type: integer
function getMonthlySiteRaised($siteid)
{
	$mysqli = connectDB();
	
	// Monthly Stats query
	$getStats = "select count(*) from recordedvisit r inner join site s on r.siteid=s.siteid where r.siteid=" . $siteid . " and id>concat(year(now()), '-', month(now()), '-01 0:0:0') group by domain;";
	
	$monthlyRaise = 0;
	
	if($result = $mysqli->query($getStats))
	{
		while($row = $result->fetch_row())
		{
			$monthlyRaise = $row[0];
		}
	}
	
	
	closeDB($mysqli);
	
	return $monthlyRaise;
}

// Return the global amount of visits recorded across floathope in the current month.
// Return type: integer
function getTotalMonthlyRaised()
{
	$mysqli = connectDB();
	
	// Monthly Stats query
	$getStats = "select count(*) from recordedvisit r inner join site s on r.siteid=s.siteid where id>concat(year(now()), '-', month(now()), '-01 0:0:0');";
	
	$monthlyRaise = 0;
	
	if($result = $mysqli->query($getStats))
	{
		while($row = $result->fetch_row())
		{
			$monthlyRaise = $row[0];
		}
	}
	
	
	closeDB($mysqli);
	
	return $monthlyRaise;
}

// When a user is registering for a new account, validate that the email doesn't already exist.
// Only one email may be associated to a userid
// Parameter type: string, email
// Return type: integer - 0 if no email exists, 1(+) if email exists
// Developer's note: there should be a regex in here to validate the email
function checkForEmail($email)
{
	$mysqli = connectDB();
	
	// Check for email
	$emailQuery = "SELECT COUNT(*) FROM user WHERE email='" . $email . "'";
	
	if($result = $mysqli->query($emailQuery))
	{
		$row = $result->fetch_row();
		
		$count = $row[0];
	}
	
	closeDB($mysqli);
	
	return $count;
}

// When creating a new user, this method will add it into the database.
//  Upon completion, the method will return the new userid
// Parameter type: string, string, string, double, double, string, integer (all required)
// Return type: integer
// Developer's note: there should be error handling for an unsuccessful push
function addNewUser($firstName, $lastName, $email, $maxChargeDollars, $chargePerVisit, $password, $charityID)
{
	$mysqli = connectDB();
	
	$insertQuery = "INSERT INTO user (first_name, last_name, email, max_charge_dollars, charge_per_visit_cents, password, charityid) ";
	
	$insertQuery .= "VALUES ('$firstName', '$lastName', '$email', '$maxChargeDollars', '$chargePerVisit', PASSWORD('$password'), '$charityID')";

	$mysqli->query($insertQuery);
	
	$useridQuery = "SELECT userid FROM user where email='$email';";
	
	if($result = $mysqli->query($useridQuery))
	{
		$row = $result->fetch_row();
		
		$userid = $row[0];
	}
	
	closeDB($mysqli);
	
	return $userid;
}

// This method adds a site to track for a given userid.
// Parameter type: integer, integer
// Developer's note: use of this method should be deprecated in favor of addUserSite
function addUserSiteByID($userid, $siteid)
{
	$mysqli = connectDB();
	
	// Update usersites
	$insertUserSiteQuery = "INSERT INTO usersites (userid, siteid) VALUES ('$userid', '$siteid');";
	$mysqli->query($insertUserSiteQuery);
	
	closeDB($mysqli);
}

// This method adds a site to a userid's account.
// Parameter type: integer, string
// Developer's note: this method should have a regex check that it is a valid domain
// Developer's note: this method should return a T/F to identify successful addition
function addUserSite($userid, $site)
{
	$mysqli = connectDB();
	
	// Check if the site is already in the system
	$checkSiteQuery = "SELECT siteid FROM site WHERE domain='" . $site . "'";
	
	if($result = $mysqli->query($checkSiteQuery))
	{
		$siteid = -1;
		
		while($row = $result->fetch_row())
		{
			$siteid = $row[0];
		}
	}
	
	// Add site to domain list if doesn't exist
	if($siteid == -1)
	{
		$addSiteQuery = "INSERT INTO site (domain) VALUES ('$site');";
		
		$mysqli->query($addSiteQuery);
		
		if($result = $mysqli->query($checkSiteQuery))
		{
			$row = $result->fetch_row();
			$siteid = $row[0];
		}
	}
	
	// Update usersites
	$insertUserSiteQuery = "INSERT INTO usersites (userid, siteid) VALUES ('$userid', '$siteid');";
	$mysqli->query($insertUserSiteQuery);
	
	closeDB($mysqli);
}

// If a user unfollows a site, remove the tracking of that domain.
// Parameter type: integer, string
// Return type: enumerated string -> "removed"
// Developer's note: there should be error handling in this method if the delete fails
function removeUserSite($userid, $domain)
{
	$mysqli = connectDB();
	
	$siteid = getSiteID($domain);
	
	$deleteQuery = "DELETE FROM usersites WHERE userid='$userid' and siteid='$siteid';";
	
	$mysqli->query($deleteQuery);
	
	closeDB($mysqli);
	
	return "removed";
}

// This method allows you to add a new domain into the database.
// Parameter type: string
// Return type: integer
function addNewDomain($domain)
{
	$mysqli = connectDB();
	$siteid = -1;
	
	// Structure query to get the new siteid after it has been added/
	$checkSiteQuery = "SELECT siteid FROM site WHERE domain='" . $domain . "'";
	
	$addSiteQuery = "INSERT INTO site (domain) VALUES ('$domain');";
	
	// add the site to database
	$mysqli->query($addSiteQuery);
	
	// get the new siteID
	if($result = $mysqli->query($checkSiteQuery))
	{
		$row = $result->fetch_row();
		$siteid = $row[0];
	}
	
	closeDB($mysqli);
	
	return $siteid;
}

// Return the siteid for any domain
// Parameter type: string
// Return type: integer
// Developer's note: should regex the domain
function getSiteID($domain)
{
	$mysqli = connectDB();
	
	// Check if the site is already in the system
	$checkSiteQuery = "SELECT siteid FROM site WHERE domain='" . $domain . "'";
	
	if($result = $mysqli->query($checkSiteQuery))
	{
		$siteid = -1;
		
		while($row = $result->fetch_row())
		{
			$siteid = $row[0];
		}
	}
	
	closeDB($mysqli);
	
	if($siteid == -1)
	{	
		return "No record";
	}
	else 
	{
		return $siteid;
	}
}

// Return the domain behind a siteID
// Parameter type: integer
// Return type: string
// Developer's note: should regex the siteid
function getSiteDomain($siteID)
{
	$mysqli = connectDB();
	
	// Check if the site is already in the system
	$checkSiteQuery = "SELECT domain FROM site WHERE siteid='" . $siteID . "'";
	
	if($result = $mysqli->query($checkSiteQuery))
	{
		$domain = -1;
		
		while($row = $result->fetch_row())
		{
			$domain = $row[0];
		}
	}
	
	closeDB($mysqli);
	
	if($domain == -1)
	{	
		return "No record";
	}
	else 
	{
		return $domain;
	}
}

// Return the total number of users who follow a given domain.
// Parameter type: string
// Return type: integer
// Developer's note: $site should be regex validated
function getSiteFollowers($site)
{
	$mysqli = connectDB();
	
	// Check if the site is already in the system
	$checkSiteQuery = "SELECT siteid FROM site WHERE domain='" . $site . "'";
	
	if($result = $mysqli->query($checkSiteQuery))
	{
		$siteid = -1;
		
		while($row = $result->fetch_row())
		{
			$siteid = $row[0];
		}
	}
	
	if($siteid == -1)
	{
		closeDB($mysqli);
		return "No record";
	}
	
	$getFollowerCountQuery = "SELECT count(*) FROM usersites WHERE siteid=$siteid";
	
	if($result = $mysqli->query($getFollowerCountQuery))
	{
		$followers = 0;
		
		while($row = $result->fetch_row())
		{
			$followers = $row[0];
		}
	}
	
	closeDB($mysqli);
	
	return $followers;
}

// Return the most popular sites on FloatHope, delimited by $count.
// Parameter type: integer
// Return type: associative array (domain->count)
function getTopSites($count)
{
	$mysqli = connectDB();
	
	// get most common sites
	$getSiteQuery = "SELECT domain, count(*) as followers FROM usersites us INNER JOIN site s ON us.siteid=s.siteid GROUP BY domain ORDER BY followers DESC LIMIT $count;";
	
	$siteArray = array();
	
	if($result = $mysqli->query($getSiteQuery))
	{
		while($row = $result->fetch_row())
		{
			$siteArray[$row[0]] = $row[1];
		}
	}
	
	closeDB($mysqli);
	
	return $siteArray;
}

// A modified version of getTopSites for the signup module. Intended to return sites with siteid.
// Parameter type: integer
// Return type: associative array (domain->siteid)
function getTopSites_SignUp($count)
{
	$mysqli = connectDB();
	
	// get most common sites
	$getSiteQuery = "SELECT domain, s.siteid, count(s.siteid) as followers FROM usersites us INNER JOIN site s ON us.siteid=s.siteid GROUP BY domain ORDER BY followers DESC LIMIT $count;";
	
	$siteArray = array();
	
	if($result = $mysqli->query($getSiteQuery))
	{
		while($row = $result->fetch_row())
		{
			$siteArray[$row[0]] = $row[1];
		}
	}
	
	closeDB($mysqli);
	
	return $siteArray;
}

// Return all charities that are available in the database.
// Return type: associative array (charityid->name)
// Developer's note: should be modified to allow for a delimeter
function getCharities()
{
	$mysqli = connectDB();
	
	// get active charities
	$getCharityQuery = "SELECT charityid, name FROM charity;";
	
	$charityArray = array();
	
	if($result = $mysqli->query($getCharityQuery))
	{
		while($row = $result->fetch_row())
		{
			$charityArray[$row[0]] = $row[1];
		}
	}
	
	closeDB($mysqli);
	
	return $charityArray;
}

// Return the name of the charity by id
// Parameter type: integer
// Return type: string
function getCharity($charityid)
{
	$mysqli = connectDB();
	
	// get charity query
	$charityQuery = "SELECT name FROM charity WHERE charityid=$charityid";
	
	$result = $mysqli->query($charityQuery);
	
	$row = $result->fetch_row();
	$charityName = $row[0];
	
	closeDB($mysqli);
	
	return $charityName;
}

// Validate whether a site is already saved
// Parameter type: string
// Return type: enum (-1 = site does not exist | 1+ = siteid)
function checkForSite($domain)
{
	$mysqli = connectDB();
	
	// get active charities
	$getSiteQuery = "SELECT siteid FROM site WHERE domain='$domain';";
	
	$siteid = -1;
	
	if($result = $mysqli->query($getSiteQuery))
	{
		while($row = $result->fetch_row())
		{
			$siteid = $row[0];
		}
	}
	
	closeDB($mysqli);
	
	return $siteid;
}

// Find domains that match a query
// Parameter type: string
// Return type: associative array (domain->count of followers)
function searchSites($query)
{
	$mysqli = connectDB();
	
	// get active charities
	$getSiteQuery = "SELECT domain, count(us.siteid) as followcount 
					FROM site INNER JOIN usersites us ON site.siteid=us.siteid
					WHERE domain LIKE '%$query%'
					GROUP BY domain
					ORDER BY followcount DESC
					LIMIT 10";
	
	$results = array();
	
	if($result = $mysqli->query($getSiteQuery))
	{
		while($row = $result->fetch_row())
		{
			$results[$row[0]] = $row[1];
		}
	}
	
	// check if sites like this exist
	$emptySiteQuery = "SELECT domain FROM site WHERE domain LIKE '%$query%' AND NOT EXISTS (SELECT * FROM usersites WHERE usersites.siteid=site.siteid) LIMIT 10";
		
	if($result = $mysqli->query($emptySiteQuery))
	{
		while($row = $result->fetch_row())
		{
			$results[$row[0]] = 0;
		}
	}
	
	closeDB($mysqli);
	
	return $results;
}

// Find users who have exceeded their monthly target
// Return type: array of userid's
function getOverageUsers()
{
	$mysqli = connectDB();
	
	// get active charities
	$usersQuery = 'SELECT r.userid, count(*), u.max_charge_dollars, u.charge_per_visit_cents 
					FROM recordedvisit r JOIN user u ON r.userid=u.userid
					WHERE r.id > CONCAT(year(now()), "-", month(now()), "-01 0:0:0") 
					GROUP BY r.userid;';
	
	$userArray = array();
	
	if($result = $mysqli->query($usersQuery))
	{
		while($row = $result->fetch_row())
		{
			$accrued = $row[1] * $row[3];
			$limit = $row[2];
			
			// record of user has accrued more than their limit
			if($accrued >= $limit)
				$userArray[$row[0]] = $limit;
		}
	}
	
	closeDB($mysqli);
	
	return $userArray;
}

// See how a user has set up their account to be charged: max limit for the month & penalty 
//  for visiting a guilty pleasures site. Both in dollar amounts.
// Parameter type: integer
// Return type: associative array (max limit for the month->penalty) | should be one row
function getChargeInfo($userid)
{
	$mysqli = connectDB();
	
	// get active charities
	$chargeQuery = "SELECT max_charge_dollars, charge_per_visit_cents FROM user WHERE userid=" . $userid . ";";
	
	$chargeArray = array();
	
	if($result = $mysqli->query($chargeQuery))
	{
		while($row = $result->fetch_row())
		{
			$chargeArray[$row[0]] = $row[1];
		}
	}
	
	closeDB($mysqli);
	
	return $chargeArray;
}

// Return a user's email and the penalty amount for visiting a guilty pleasure.
// Parameter type: integer
// Return type: array [0] = email, [1] = penalty amount
function getEmailAndCharge($userid)
{
	$mysqli = connectDB();
	
	// get active charities
	$chargeQuery = "SELECT email, charge_per_visit_cents FROM user WHERE userid=" . $userid . ";";
	
	$chargeArray = array();
	
	if($result = $mysqli->query($chargeQuery))
	{
		while($row = $result->fetch_row())
		{
			$chargeArray[0] = $row[0];
			$chargeArray[1] = $row[1];
		}
	}
	
	closeDB($mysqli);
	
	return $chargeArray;
}

// Return charity, penalty amount and monthly dollar limit for a user.
// Parameter type: integer
// Return type: array [0]=string, [1]=double,  [2]=double
function getProfileInfo($userid)
{
	$mysqli = connectDB();
	
	// get active charities
	$profileQuery = "SELECT c.name, charge_per_visit_cents, max_charge_dollars
					FROM user u JOIN charity c ON u.charityid = c.charityid
					WHERE userid=$userid;";
	
	$profileArray = array();
	
	if($result = $mysqli->query($profileQuery))
	{
		while($row = $result->fetch_row())
		{
			$profileArray[0] = $row[0]; // charity
			$profileArray[1] = $row[1]; // charge per visit
			$profileArray[2] = $row[2]; // monthly limit
		}
	}
	
	closeDB($mysqli);
	
	return $profileArray;
}

// Determine whether the user has been notified of their monthly charge.
// Parameter type: integer
// Return type: boolean
function notificationSentThisMonth($userid)
{
	$mysqli = connectDB();
	
	// check notification
	// get year & month
	$date = date("Y-m");
	$checkQuery = "SELECT * FROM notification WHERE notification='$date' AND userid=$userid";
	
	if($result = $mysqli->query($checkQuery))
	{
		if($result->num_rows == 0)
		{
			$flag = false;
		}
		else 
		{
			$flag = true;
		}
	}
	
	closeDB($mysqli);
	
	return $flag;
}

// Save that the user was notified about their charge.
// Parameter type: integer, integer, double
// Developer's note: this table was not created in create-tables.sql and is not 
//  currently supported.
function addNotification($userid, $total, $chargePer)
{
	$mysqli = connectDB();
	
	$insertQuery = "INSERT INTO notification (notification, userid, amount, cost_per_charge, timestamp)";
	
	$date = date("Y-m");
	$insertQuery .= " VALUES ('$date', '$userid', '$total', '$chargePer', NOW())";
	
	$mysqli->query($insertQuery);
	
	closeDB($mysqli);
}

// Update the user's selected charity.
// Parameter type: integer, integer
// Developer's note: should think through how to create an audit trail of changes over time
function updateCharity($userid, $charityid)
{
	$mysqli = connectDB();
	
	$updateQuery = "UPDATE user SET charityid=$charityid WHERE userid=$userid";
	
	$mysqli->query($updateQuery);
	
	closeDB($mysqli);	
}

// Update the user's payment amount: penalty rate and monthly limit
// Parameter type: integer, double, double
function updatePaymentInfo($userid, $chargePer, $monthlyLimit)
{
	$mysqli = connectDB();
	
	$updateQuery = "UPDATE user SET charge_per_visit_cents=$chargePer, max_charge_dollars=$monthlyLimit 
					WHERE userid=$userid";
	
	$mysqli->query($updateQuery);
	
	closeDB($mysqli);	
}

// Log in the user based on their email & password combination
// Parameter type: string, string
// Return type: boolean (true=successful login, false=invalid email/password combo)
function getLogin($email, $password)
{
	// connect to database
	$mysqli = connectDB();
	
	$verifyLogin = "SELECT userid, password FROM user WHERE email='" . $email . "' AND password=PASSWORD('" . $password . "');";
	
	if($result = $mysqli->query($verifyLogin))
	{
		if($result->num_rows == 0)
			$passLogin = false;
		else 
		{
			while($row = $result->fetch_row())
			{
				$userid = $row[0];
				$encryptedPassword = $row[1];
			}
			
			$passLogin = true;
		}
		
		$result->close();
	}
	
	closeDB($mysqli);
	
	return $passLogin;
}

// Update the user's password
// Parameter type: integer, string
// Developer's note: should regex both parameters to prevent corrupting user account
function updatePassword($userid, $password)
{
	$mysqli = connectDB();
	
	$updateQuery = "UPDATE user SET password=PASSWORD('$password') 
					WHERE userid=$userid";
	
	$mysqli->query($updateQuery);
	
	closeDB($mysqli);
}

?>