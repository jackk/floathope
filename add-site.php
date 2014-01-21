<?php
// @author jack krawczyk -- @jackk
// last updated: Sep 10, 2012

// This file is used as a module for a user to add a site that doesn't currently
//  exist in the floathope index.
// There is no UI for this page -- it simply receives a site to add then redirects
//  the user to the newly added site's profile page.

	// includes for php
	include 'includes.php';
	
	// reroute if not logged in
	if(! isset($_COOKIE['EMAIL_floathope']))
	{
		header("Location: index.php");
	}
	
	$newSite = $_POST['site'];
	
	//echo "$search is the query";
	
	if(preg_match('|^[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $newSite))
	{
		// add domain
		$siteid = addNewDomain($newSite);
		
		// add site for user
		// parse out userid from userCookie
		$userArray = json_decode(htmlspecialchars_decode($_COOKIE['USER_floathope']));
	
		//get user id
		$userid = $userArray->{'userid'};
		
		// add site to user account
		addUserSiteByID($userid, $siteid);
		
		// update user cookie to have new site
		$user = getUserFromDB($userid);
		$past = time() - 100;
		setcookie(USER_floathope, gone, $past);
		$thirtyDays = time() + 60*60*24*30;
		setcookie(USER_floathope, htmlspecialchars($user->printUserJSON()), $thirtyDays);
		
		// forward user to indulgence page
		header("Location: site.php?ind=$newSite");
	}
	
?>