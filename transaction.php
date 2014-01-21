<?php
// submit donation
function donate($user, $charity, $amount)
{
	// dev URL
	$url = "http://usapisandbox.fgdev.net/donation/creditcard";
	// prod URL
	// $url = "https://api.firstgiving.com/donation/creditcard";
	
	$fields = array(
		'ccNumber' => urlencode("4457010000000009"),
		'ccType' => urlencode("VI"),
		'ccExpDateYear' => urlencode("14"),
		'ccExpDateMonth' => urlencode("01"),
		'ccCardValidationNum' => urlencode("222"),
		'billToFirstName' => urlencode("Jack"),
		'billToLastName' => urlencode("Krawczyk"),
		'billToAddressLine1' => urlencode("1 Main St."),
		'billToCity' => urlencode("Burlington"),
		'billToState' => urlencode("MA"),
		'billToZip' => urlencode("01803"),
		'billToCountry' => urlencode("US"),
		'billToEmail' => urlencode("jacek.krawczyk@gmail.com"),
		'remoteAddr' => urlencode(getenv('REMOTE_ADDR')),
		'amount' => urlencode("25.00"),
		'currencyCode' => urlencode("USD"),
		'charityId' => urlencode("dbcefd6e-2023-11e0-a279-4061860da51d"),
		'eventId' => urlencode(""),
		'description' => urlencode("This is a donation from Float Hope")
	);
	
	//url-ify the data for the POST
	foreach($fields as $key=>$value) { $fields_string .= $key.'='.$value.'&'; }
	$fields_string = rtrim($fields_string,'&');
	
	//open connection
	$ch = curl_init();
	
	//set the url, number of POST vars, POST data
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_POST,count($fields));
	curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	// set the API key (dev box)
	curl_setopt($ch,CURLOPT_HTTPHEADER,array("JG_APPLICATIONKEY: 16b4fdf2-2514-11e1-837a-12313b003616", "JG_SECURITYTOKEN: 16b7a296-2514-11e1-837a-12313b003616"));
	// set the API key (prod)
	// curl_setopt($ch,CURLOPT_HTTPHEADER,array("JG_APPLICATIONKEY: ", "JG_SECURITYTOKEN: "));
	
	//execute post
	$result = curl_exec($ch);
	
	if(!curl_errno($ch))
	{	
		//$info = curl_getinfo($ch);
		//echo "http code = " . $info['http_code'] . "<br />";
		
		// transform POST results into XML
		$sxml = simplexml_load_string($result);
		// transform XML to JSON (intermediate step needed, no good XML->array methods)
		$json = json_encode($sxml);
		// transform JSON to array
		$arr = json_decode($json, true);
	
		// set the transaction id
		$transactionid = $arr['firstGivingResponse']['transactionId'];
		
		//echo "transaction id = " . $transactionid;
		var_dump($arr);
	}
	else
	{
		echo "Curl failed";
		$transactionid = "Failed";
	}
	
	
	
	//close connection
	curl_close($ch);

	
	return $transactionid;
}

donate("1", "1", "1");

// submit recurring donation

?>