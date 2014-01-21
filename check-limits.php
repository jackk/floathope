<?php
// @author jack krawczyk -- @jackk
// last updated: Jun 8, 2013

// This file is meant to run as a cron job nightly to identify how many users have
//  reached beyond their monthly penalty limit, and then send an email to notify them.
//  It also works at the end of the month to notify how many penalties they incurred
//  over the span of the month. Users' accounts are reset at the start of every month.
// It prints debugging text to identify who has successfully received their updates.
// Developer's note: I never put this into full production. Please

	//includes
	include 'includes.php';
	
	$users = getOverageUsers();
	$i = 1;
	
	foreach($users as $userid => $total)
	{
		$date = date("Y-m");
		echo "<p>$i - $userid - $total - $date </p>\n";
		
		// first item = email | second item = charge | third item = charity (tbd)
		$userInfo = getEmailAndCharge($userid);
		$email = $userInfo[0];
		$chargePer = $userInfo[1];
		
		// make sure user does not have notification sent already
		if(! notificationSentThisMonth($userid))
		{
			// send email notification
			$headers = "From: account@floathope.com \r\n Reply-To: account@floathope.com \r\n X-Mailer: PHP/" . phpversion();
			mail($email, "Your guilty pleasure kharma tax is ready.", "You are ready to pay \$$total for (charity).", $headers);
			
			// add to notifications table
			addNotification($userid, $total, $chargePer);
			
			echo "<p>EMAILED AND UPDATED</p>\n";
		}
		else 
		{
			echo "<p>NO EMAIL SENT OR UPDATE</p>\n";
		}
		
		$i++;
	}
	
?>