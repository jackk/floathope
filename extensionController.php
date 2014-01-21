<?php
	include 'includes.php';
	
	if(! isset($_COOKIE['USER_floathope']))
	{
		header("Location: index.php");
	}
	
	$userJSON = htmlspecialchars_decode($_COOKIE['USER_floathope']);
	
	echo $userJSON;
	
	// echo $user as JSON (json_encode($array));
	// need to use associative array
?>