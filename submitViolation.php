<?php
// @author jack krawczyk -- @jackk
// last updated: Mar 3, 2012

// This is the module for recording when a logged in user accesses a guilty pleasure.
// It is called from background.html within the Chrome Extension.

	include 'database.php';

	if($_POST['userid'] != null)
	{
		$site = $_POST['site'];
		$userid = $_POST['userid'];
		$timeStamp = $_POST['timestamp'];
		
		// $returnString = "user id = " . $userid . "\n site hit = " . $site . "\n timeStamp = " . $timeStamp;
		
		// echo "The return string is: \n" . $returnString;
		
		$mysqli = connectDB();
		
		// ******************
		// get the siteid
		// ******************
		$getSiteIdQuery = "SELECT siteid FROM site where domain='" . $site . "';";
		
		$siteid = "-1";
		
		if($result = $mysqli->query($getSiteIdQuery))
		{
			while($row = $result->fetch_row())
			{
				$siteid = $row[0];
			}
			
			$result->close();
		}
		
		// *******************
		// update the database
		// *******************
		$updateQuery = "INSERT INTO recordedvisit (id, siteid, userid) values (NOW(), " .
			$siteid . ", " . $userid . ");";

		if($mysqli->query($updateQuery) == TRUE)
		{
			$feedback = "Naughty naughty! You just visited " . $site;
		}
		else
		{
			$feedback = "Failed to update database";
		}
		
		closeDB($mysqli);
		
		echo $feedback;
	}

?>