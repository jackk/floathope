<?php
// @author jack krawczyk -- @jackk
// last updated: Nov 28, 2012

// this file is meant to be a central controller to include all objects & methods

	include 'user.php'; // central user object definition
	include 'database.php'; // controller for all database methods
	
	// If you use Google Analytics, insert your Urchin code below where listed as UA-XXXXX
	// Simple return of script into HTML rendering -- include right before your </head> tag
	function getGoogleAnalytics()
	{
		echo "<script type=\"text/javascript\">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-XXXXX']);
		  _gaq.push(['_trackPageview']);
		
		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		
		</script>";
	}
	
?>