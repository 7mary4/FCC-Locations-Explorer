<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">                                             
	<?php
	 
	//=================
	// Basic API to simplexml function
	//=================
	function My_simplexml_load_file($URL)
	  {
	  $ch = curl_init($URL);

	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_HEADER, 0);
	  $xml = simplexml_load_string(curl_exec($ch));
	  curl_close($ch);
	  return $xml;
	  }  
	
	

	/**
	 * Splits up a string into an array similar to the explode() function but according to CamelCase.
	 * Uppercase characters are treated as the separator but returned as part of the respective array elements.
	 * @author Charl van Niekerk <charlvn@charlvn.za.net>
	 * @param string $string The original string
	 * @param bool $lower Should the uppercase characters be converted to lowercase in the resulting array?
	 * @return array The given string split up into an array according to the case of the individual characters.
	 */
	function explodeCase($string, $lower = true)
	{
	  // Split up the string into an array according to the uppercase characters
	  $array = preg_split('/([A-Z][^A-Z]*)/', $string, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

	  // Convert all the array elements to lowercase if desired
	  if ($lower) {
	    $array = array_map(strtolower, $array);
	  }

	  // Return the resulting array
	  return $array;
	} 
	/*   
	    * bytes() function
		* number format and add the appropriate abbreviation for test results
	*/   
	
	   function bytes($a) {
	    $unim = array("B","KB","MB","GB","TB","PB");
	    $c = 0;
	    while ($a>=1024) {
	        $c++;
	        $a = $a/1024;
	    }
	    return number_format($a,($c ? 2 : 0),",",".")." ".$unim[$c];
	}
	  // set some default values
	  $cityInfo = null; 
	  $safeLocation ="";
 
	  if(isset($_GET['city'])) {   
		$encodedCity = urlencode($_GET['city']);
			 
			$apiURL =  "http://query.yahooapis.com/v1/public/yql?q=use%20%22http%3A%2F%2Fdoglr.com%2Ffcc%2Ffcc-cons-bb-test.xml%22%20AS%20fcc.bbtest%3B%20select%20*%20from%20query.multi%20where%20queries%3D'%0A%09select%20*%20from%20fcc.bbtest%20where%20(latitude%2C%20longitude)%20in%20(select%20centroid.latitude%2C%20centroid.longitude%20from%20geo.places%20where%20text%3D%22" . $encodedCity . "%22)%3B%0A%09select%20*%20from%20geo.places(1)%20where%20text%3D%22" . $encodedCity . "%22%0A%09'&env=store%3A%2F%2Fdatatables.org%2Falltableswithkeys";
	    $cityInfo = My_simplexml_load_file($apiURL);    
     	$safeLocation = htmlspecialchars($_GET['city']);

		foreach($cityInfo->results->results as $results){ 
			if(isset($results->SpeedTestCounty)) { 
				$tests = (array) $results->SpeedTestCounty;
            }	
            elseif(isset($results->place)){ // we'll use this in the aside. 
			    $placeInfo = (array) $results->place;  
			}
        } // end foreach results
      
      }  // end if get.city
	                                                                                     
	
	
	?>
    
	<title>
		<?php
		if(!empty($safeLocation))  {
		  $title = "FCC Licenses for  $safeLocation"  ;
		}
		else{
		  $title = 'Find the FCC licenses for a city';
		}
		print $title;
		?>
	</title>  
	<meta name="description" content="This page will display FCC information about a city"> 
	
	<!--the following javascript needs to go in the head. it lets i.e. recognize the new html5 tags -->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	 
	<!-- CSS from Yahoo's YUI3 library. These set the baseline styles, fonts, and grids. -->
	<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/combo?3.2.0/build/cssfonts/fonts-min.css&3.2.0/build/cssreset/reset-min.css&3.2.0/build/cssgrids/grids-min.css&3.2.0/build/cssbase/base-min.css">  
	<!-- location hack specific styles -->
	<link rel="stylesheet" type="text/css" href="location.css">  
</head>
<body class="yui3-g">  
	<header role="banner" class="yui3-u-1">
		<h1>License information for <?php print $safeLocation; ?></h1>  
		<div id="cityChooser">
			<div id="status">Checking location</div>  
			<form action="/examples/location/" role="search">
				<label for="city">Search for a city</label>
				<input type="search" name="city" id="city" required placeholder="New York" >
				<button>Submit</button>
			</form>
		</div>	
	</header>
	<section role="main" class="yui3-u-2-3">
		      
		<?php  
		if(!empty($_GET['city'])){
			?>                                 
		<header><h1>Licensing and other data about <?php print $safeLocation; ?></h1></header> 
	   
		<section class="tests">
		<header><h1>Broadband tests</h1></header>
		<dl>
		<?php
	foreach ($tests as $test =>$value){ 
		//explodeCase changes camel case to an array, implode puts it back into a sentence.  and then uppercase first letter
		$testFormatted = ucwords( implode(' ', explodeCase( $test, false ) ) );
		$valueFormatted = bytes($value);// show kb or mb if appropriate 
		print "<dt>$testFormatted</dt><dd>$valueFormatted</dd>"; 
	} 
		?>
		
		</dl>
		<p>This section is powered by the FCC's Broadband Test API</p>
		</section>  
		<?php
	}
	?>
		
		
	 
		
		
	</section>
	<aside role="complementary" class="yui3-u-1-3">
		<?php  
		if(!empty($_GET['city'])){
			?>
		<table summary="this table summarizes the geographic details for <?print $safeLocation; ?>">  
			<thead>
				<tr>
					<th scope="col">Detail</th>
					<th scope="col">Value</th>
				</tr>
			</thead> 
			<tbody>
				<tr>
					<td>Location</td>
					<td><?php print $placeInfo['name'] . ', '. $placeInfo['admin1'] ;?></td>
				</tr> 
				<tr>
					<td>WOEID</td>
					<td><?php print $placeInfo['woeid']; ?></td>
				</tr>
				<tr>
					<td>Area Rank</td>
					<td><?php print $placeInfo['areaRank']; ?></td>
				</tr> 
				<tr>
					<td>Population Rank (for the United States)</td>
					<td><?php print $placeInfo['popRank']; ?></td>
				</tr>								
			</tbody>
		</table>
		<?php
}// end if get:city
		?>
	</aside>
	<footer role="content-info" class="yui3-u-1">copyright 2010</footer>       
	<script src="location.js"></script> 
</body>
</html>
