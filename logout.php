<?php
	// check if user is already signed in
	if(! isset($_COOKIE['USER_floathope']))
	{
		// already is logged out, redirect to index.php
		header("Location: index.php");
	}
	else 
	{
		// destroy cookies
		$past = time() - 100;
		
		setcookie(EMAIL_floathope, gone, $past);
		setcookie(KEY_floathope, gone, $past);
		setcookie(USER_floathope, gone, $past);
		
		//echo "<p>You have been logged out.</p><a href='index.php'>Home Page</a></p>";
		
		header("Location: index.php");
	}

?>