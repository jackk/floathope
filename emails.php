<?php
	include 'includes.php';
	
	$mysqli = connectDB();
	
	$query = "SELECT userid, email, first_name, last_name, charityid FROM user ORDER BY userid DESC";
	
	$result = $mysqli->query($query);
	
	while($row = $result->fetch_row())
	{
		echo "<li>";
		foreach($row as $item)
		{
			echo "$item ";
		}
		echo "<br />";
	}
?>