<?php
// @author jack krawczyk -- @jackk
// last updated: Sep 10, 2012

// This is the search module for domains for users who are looking to find a new site to follow.

	// includes for php
	include 'includes.php';
	
	// reroute if not logged in
	if(! isset($_COOKIE['EMAIL_floathope']))
	{
		header("Location: index.php");
	}
	
	$search = $_POST['query'];
	
	//echo "$search is the query";
	
	if(preg_match('|^[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $search))
	{
		//echo "<strong>$search</strong> is a valid query";
		$results = searchSites($search);
		
		// start table
			echo "<table class='table table-striped'>
				<thead>
					<tr>
						<th>Indulgence</th>
						<th style='text-align:center;'>Total Followers</th>
					</tr>
				</thead>
				<tbody>";
			
		if(count($results) == 0)
		{
			echo "<tr><td>$search</td><td style='text-align:center;'>0 
				<form action='add-site.php' method='post'>
				  <input type='hidden' name='site' value='$search' />
				  <input type='submit' class='btn btn-mini btn-primary' value='+ add it' />
				</form></td></tr>";
		}
		else 
		{
			echo "<strong>" . count($results) . "</strong> sites match your query.";
			
			// submit table info
			foreach($results as $domain => $followers)
			{
				echo "<tr><td><a href='site.php?ind=$domain'>" . $domain . "</a></td><td style='text-align:center;'>" . $followers . "</td></tr>";
			}
			
			
		}
		
		// close table
		echo "</tbody></table>";
	}
	else // invalid characters 
	{
		echo "<font color='#680000'>Child, please. Please enter a search term without crazy characters.</font>";
	}
	
?>