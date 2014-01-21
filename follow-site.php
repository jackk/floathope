<?php
// @author jack krawczyk -- @jackk
// last updated: Sep 10, 2012

// This is a UI-less script that follows/unfollows a site on behalf of a user.

// includes for php
	include 'includes.php';
	
	// reroute if not logged in
	if(! isset($_COOKIE['EMAIL_floathope']))
	{
		header("Location: index.php");
	}
	
	// get domain to add
	$domain = $_POST['domain'];
	
	// determine action
	$type = $_POST['type'];
	
	// parse out userid from userCookie
	$userArray = json_decode(htmlspecialchars_decode($_COOKIE['USER_floathope']));
	
	//get user id information
	$userid = $userArray->{'userid'};
	
	
	
	//echo "$uid user id \n $domain";
	if($type == "follow")
	{
		//echo "added $domain for user id = $userid";
		addUserSite($userid, $domain);
		
		// update user cookie to have new site in tracking
                $user = getUserFromDB($userid);
                $past = time() - 100;
                setcookie(USER_floathope, gone, $past);
                $thirtyDays = time() + 60*60*24*30;
                setcookie(USER_floathope, htmlspecialchars($user->printUserJSON()), $thirtyDays);
                echo "\ncookie is set";
                echo "\nadded $domain for user id = $userid";
	}
	elseif($type == "unfollow")
	{
		
		removeUserSite($userid, $domain);
		
		// update user cookie to not have this site in tracking
		$user = getUserFromDB($userid);
		$past = time() - 100;
		setcookie(USER_floathope, gone, $past);
		$thirtyDays = time() + 60*60*24*30;
		setcookie(USER_floathope, htmlspecialchars($user->printUserJSON()), $thirtyDays);
		echo "\ncookie is set";
		echo "\nremoved $domain for user id = $userid";
	}
	else 
	{
		echo "no data";
	}
?>
